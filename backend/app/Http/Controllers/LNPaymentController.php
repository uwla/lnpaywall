<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LNPaymentController extends Controller
{
     public function pay(Request $request) {
        $data = [];
        $fields = ['amount', 'invoiceId', 'invoiceRequest'];
        $hasInvoice = true;

        foreach ($fields as $field) {
            if (! $request->has($field))
                $hasInvoice = false;
            else
                $data[$field] = $request->get($field);
        }

        if (! $hasInvoice || $request->has('time')) {
            $time = 20;
            if ($request->has('time')) {
                $request->validate([
                    'time' => 'integer|min:4|max:1000'
                ]);
                $time = $request->time;
            }
            $satoshis_per_second = 10;
            $new_amount = $time * $satoshis_per_second;

            $invoice = $this->getInvoice($new_amount);
            $data['amount'] = $invoice['tokens'];
            $data['invoiceId'] = $invoice['id'];
            $data['invoiceRequest'] = $invoice['request'];
        }

        $data['time'] = $data['amount']/10;
        $data['qrcode'] = $this->qrcode($data['invoiceRequest']);

        return view('pay', $data);
    }

    public function getInvoice($amount = 300) {
        $response = Http::post(config('lnserver.endpoint.invoice.new'), [
            'amount' => $amount
        ]);
        $invoice = $response['invoice'];
        return $invoice;
    }

    public function confirmPayment(Request $request) {
        $request->validate([
            'invoiceId' => 'string|required|min:1',
            'invoiceRequest' => 'string|required|min:1',
        ]);
        $invoiceId = $request->invoiceId;
        $invoiceRequest = $request->invoiceRequest;

        $res = Http::post(config('lnserver.endpoint.invoice.status'), [
            'invoiceId' => $invoiceId
        ]);

        if ($request->wantsJson()) {
            return [ 'confirmed' => $res['is_confirmed'] ];
        }

        $paid = $res['is_confirmed'];
        $amount = $res['tokens'];
        $time = $amount/10;

        $val = '';
        if ($paid) $val = 'y';
        else $val = 'n';

        $session = $request->session();
        $session->put('paid', $val);
        $session->put('timepaid', $time);

        return view('confirm', compact('paid', 'amount', 'invoiceId', 'invoiceRequest'));
    }

    public function qrcode($str) {
        return QrCode::size(250)->generate($str);
    }
}
