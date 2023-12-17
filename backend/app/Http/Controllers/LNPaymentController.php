<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LNPaymentController extends Controller
{
    /**
     * The amount of satoshis paid per second of access.
     *
     * @var string
     */
    protected $satoshis_per_second = config('lnpaywall.payment.satoshis_per_second');

    /**
     * Show the payment page.
     */
    public function pay(Request $request)
    {
        $data = [];
        $fields = ['amount', 'invoiceId', 'invoiceRequest'];
        $hasInvoice = true;

        foreach ($fields as $field) {
            if (!$request->has($field))
                $hasInvoice = false;
            else
                $data[$field] = $request->get($field);
        }

        if (!$hasInvoice || $request->has('time')) {
            $time = 20;
            if ($request->has('time')) {
                $request->validate([
                    'time' => 'integer|min:4|max:1000'
                ]);
                $time = $request->time;
            }
            $new_amount = $time * $this->satoshis_per_second;

            $invoice = $this->getInvoice($new_amount);
            $data['amount'] = $invoice['tokens'];
            $data['invoiceId'] = $invoice['id'];
            $data['invoiceRequest'] = $invoice['request'];
        }

        $data['time'] = $data['amount'] / $this->satoshis_per_second;
        $data['qrCode'] = $this->qrCode($data['invoiceRequest']);

        return view('pay', $data);
    }

    /**
     * Get LN invoice for the given amount.
     */
    public function getInvoice($amount = 300)
    {
        $response = Http::post(config('lnpaywall.endpoint.invoice.new'), [
            'amount' => $amount
        ]);
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

        $res = Http::post(config('lnpaywall.endpoint.invoice.status'), [
            'invoiceId' => $invoiceId
        ]);

        if ($request->wantsJson()) {
            return ['confirmed' => $res['is_confirmed']];
        }

        $paid = $res['is_confirmed'];
        $amount = $res['tokens'];
        $time = $amount / $this->satoshis_per_second;

        $val = '';
        if ($paid)
            $val = 'y';
        else
            $val = 'n';

        $session = $request->session();
        $session->put('paid', $val);
        $session->put('time_paid', $time);

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
