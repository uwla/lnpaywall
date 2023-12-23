<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LNPAYWALL</title>
    </head>
    <body>
        @include('style')
        <main>
            <h1>PAY TO RECEIVE ACCESS</h1>

            <p>Pay {{ $amount }} to the following invoice:</p>

            <pre id="qrCode">{{ $qrCode }}</pre>

            <div class="hash-content">
                <pre id="pay-req" class="hash" rows="5">{{ $invoiceRequest  }}</pre>
                <button class="copy-hash">Copy</button>
                <span class="copied" >COPIED!</span>
            </div>

            <p>After paying, you will have {{ $hours }} hours and {{ $minutes }} minutes to enjoy the website.</p>

            <form action="/lnpay/pay" method="POST">
                @csrf

                <div id="time-box">
                    <p>Or, request a new invoice:</p>
                    <span>
                        <input class="time-input" type="number" min="1" max="23"
                            name="hours" value="{{ $hours }}" />
                        <label for="minutes">HOURS</label>
                    </span>
                    <span>
                        <input class="time-input" type="number" min="0" max="59"
                            name="minutes" value="{{ $minutes }}" />
                        <label for="minutes">MINUTES</label>
                    </span>
                    <button id="btn-new-invoice">REQUEST NEW INVOICE</button>
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
                    <p>If you have paid, click here:</p>
                    <button id="btn-confirm-payment">I CONFIRM I HAVE PAID</button>
                </noscript>
            </form>

        </main>
        @include('script')
    </body>
</html>
