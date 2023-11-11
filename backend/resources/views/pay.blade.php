<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LNPAYWALL</title>
    </head>
    <body>
        @include('style')
        <main class="container">

            <div class="pay-area">

                <h1 class="title">PAY TO RECEIVE ACCESS</h1>

                <p class="text">Pay {{ $amount }} to the following invoice:</p>

                <pre class="qr-code">{{ $qrcode }}</pre>

                <div class="hash-content">
                    <pre id="payreq" class="hash" rows="5">{{ $invoiceRequest  }}</pre>
                    <button class="copy-hash">Copy</button>
                    <span class="copied" >COPIED!</span>
                </div>

            </div>

            <p>After paying, you will have {{ $time }} seconds to enjoy the website.</p>

            <form action="/lnpay/pay" method="POST">
                @csrf
                <input type="hidden" value="{{ $time }}" name="time" id="time">

                <div class="time-box">
                    <p class="text">SET THE DESIRE NUMBER OF SECONDS</p>
                    <input type="number" min="1" max="100" value="{{ $time }}" name="time" id="time" />
                </div>

                <div class="reload-box">
                    <button class="btn btn-reload">REQUEST NEW INVOICE</button>
                </div>


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

            </form>

            <form action="/lnpay/confirm" method="POST" id="confirm-payment">
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
                <noscript>
                <button>I CONFIRM I HAVE PAID</button>
                </noscript>
            </form>

        </main>
        @include('script')
    </body>
</html>
