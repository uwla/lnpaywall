<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LNPaymentController extends Controller
{
    public function hasPaid(Request $request) {
        $session = $request->session();
        if ('y' != $session->get('paid', 'n')) {
            return false;
        }
        if (time() > $session->get('expire_at', 0)) {
            return false;
        }
        return true;
    }

    public function auth(Request $request) {
        if ($this->hasPaid($request)) {
            return response('', 204);
        }
        return response('', 401);
    }

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
        $response = Http::post('lnserver:8080/api/invoices', [
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

        $res = Http::post('lnserver:8080/api/invoices/status', [
            'invoiceId' => $invoiceId
        ]);

        if ($request->wantsJson()) {
            return [ 'confirmed' => $res['is_confirmed'] ];
        }

        $paid = $res['is_confirmed'];
        $amount = $res['tokens'];
        $timeLeft = $amount/10;

        $val = '';
        if ($paid) $val = 'y';
        else $val = 'n';

        $session = $request->session();
        $session->put('paid', $val);
        $session->put('expire_at', time() + $timeLeft);

        return view('confirm', compact('paid', 'amount', 'invoiceId', 'invoiceRequest'));
    }

    public function qrcode($str) {
        return QrCode::size(250)->generate($str);
    }
}
