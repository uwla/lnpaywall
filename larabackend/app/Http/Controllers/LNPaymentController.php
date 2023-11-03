<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LNPaymentController extends Controller
{
    public function auth(Request $request) {
        if ($request->session()->get('paid', 'n') == 'y') {
            return response('', 204);
        }
        return response('', 401);
    }

    public function pay() {
        $invoice = $this->getInvoice();
        return view('pay', [
            'invoiceId' => $invoice['id'],
            'invoiceRequest' => $invoice['request'],
            'qrcode' => $this->qrcode($invoice['request']),
            'paid' => false,
        ]);
    }

    public function getInvoice() {
        return Http::post('lnserver:8080/api/invoices')['invoice'];
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

        $paid = $res['is_confirmed'];

        $val = '';
        if ($paid) $val = 'y';
        else $val = 'n';

        $request->session()->put('paid', $val);

        $qrcode = $this->qrcode($invoiceRequest);

        return view('pay', compact('paid', 'invoiceId', 'invoiceRequest', 'qrcode'));
    }

    public function qrcode($str) {
        return QrCode::size(250)->generate($str);
    }
}
