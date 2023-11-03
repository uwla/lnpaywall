<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LNPAY</title>
    </head>
    <body>
        <main>
            <h1>PAYMENT DUE</h1>
            <p>Pay {{ $amount }} to the following invoice:</p>
            {{ $qrcode }}
            <pre>{{ $invoiceRequest }}</pre>
            <p>After paying, you will have {{ $time }} seconds to enjoy the website.</p>
            <form action="/lnpay/pay" method="POST">
                @csrf
                <label for="time">SET THE DESIRE NUMBER OF SECONDS</label>
                <input type="number" min="5" max="100" value="{{ $time }}" name="time" id="time">
                <input type="number"
                    hidden
                    name="amount"
                    value="{{ $amount }}"
                    style="display: none">
                <input type="text"
                    hidden
                    name="invoiceId"
                    value="{{ $invoiceId }}"
                    style="display: none">
                <input type="text"
                    hidden
                    name="invoiceRequest"
                    value="{{ $invoiceRequest }}"
                    style="display: none">
                <button>REQUEST NEW INVOICE</button>
            </form>
            <br>

            <form action="/lnpay/confirm" method="POST">
                @csrf
                <input type="number"
                    hidden
                    name="amount"
                    value="{{ $amount }}"
                    style="display: none">
                <input type="text"
                    hidden
                    name="invoiceId"
                    value="{{ $invoiceId }}"
                    style="display: none">
                <input type="text"
                    hidden
                    name="invoiceRequest"
                    value="{{ $invoiceRequest }}"
                    style="display: none">
                <button>I CONFIRM I HAVE PAID</button>
            </form>
        </main>
    </body>
</html>
