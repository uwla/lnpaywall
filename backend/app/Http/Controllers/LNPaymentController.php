<?php

namespace App\Http\Controllers;

use App\SessionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LNPaymentController extends Controller
{
    /**
     * Show the payment page.
     */
    public function pay(Request $request)
    {
        $data = [];
        $fields = ['amount', 'invoiceId', 'invoiceRequest'];
        $hasInvoice = true;
        $satoshis_per_second = config('lnpaywall.payment.satoshis_per_second');
        $min_time = config('lnpaywall.payment.min_seconds');

        foreach ($fields as $field) {
            if (!$request->has($field))
                $hasInvoice = false;
            else
                $data[$field] = $request->get($field);
        }

        # if there is no invoice, or if the user requests a new one
        if (!$hasInvoice || $request->has('minutes') || $request->has('hours')) {
            # validation
            $request->validate([
                'minutes' => 'nullable|integer|min:0|max:59',
                'hours' => 'nullable|integer|min:0|max:23',
            ]);

            # convert units to seconds
            $minutes = $request->get('minutes', 0);
            $hours = $request->get('hours', 0);
            $time = 60*$minutes + 3600*$hours;

            # minimum time
            if ($time < $min_time)
                $time = $min_time;

            # compute amount
            $new_amount = $time * $satoshis_per_second;

            # generate new invoice
            $invoice = $this->getInvoice($new_amount);
            $data['amount'] = $invoice['tokens'];
            $data['invoiceId'] = $invoice['id'];
            $data['invoiceRequest'] = $invoice['request'];
        }

        $time = $data['amount'] / $satoshis_per_second;
        $data['hours'] = (int) ($time / 3600);
        $data['minutes'] = (int) (($time % 3600) / 60);
        $data['qrCode'] = $this->qrCode($data['invoiceRequest']);

        return view('pay', $data);
    }

    /**
     * Get LN invoice for the given amount.
     */
    public function getInvoice($amount = 300)
    {
        $endpoint_invoice_new = config('lnpaywall.endpoint.invoice.new');
        $response = Http::post($endpoint_invoice_new, ['amount' => $amount]);
        $invoice = $response['invoice'];
        return $invoice;
    }

    /**
     * Confirm payment has been made.
     * Return either JSON or confirmation webpage, depending on request headers.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'invoiceId' => 'string|required|min:1',
            'invoiceRequest' => 'string|required|min:1',
        ]);
        $invoiceId = $request->invoiceId;
        $invoiceRequest = $request->invoiceRequest;
        $satoshis_per_second = config('lnpaywall.payment.satoshis_per_second');
        $endpoint_invoice_status = config('lnpaywall.endpoint.invoice.status');

        $res = Http::post($endpoint_invoice_status, ['invoiceId' => $invoiceId]);

        if ($request->wantsJson()) {
            return ['confirmed' => $res['is_confirmed']];
        }

        $paid = $res['is_confirmed'];
        $amount = $res['tokens'];
        $time = $amount / $satoshis_per_second;

        $val = 'n';

        if ($paid) {
            $val = 'y';
            SessionManager::markSessionPaymentAsConfirmed();
            SessionManager::setSessionPaidTime($time);
            SessionManager::startSession();
        }

        return view('confirm', compact('paid', 'amount', 'invoiceId', 'invoiceRequest'));
    }

    /**
     * Generate QR Code SVG for the given string, to be displayed as HTML in payment page.
     */
    public function qrCode($str)
    {
        return QrCode::size(250)->generate($str);
    }
}
