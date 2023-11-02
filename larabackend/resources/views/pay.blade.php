<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LNPAY</title>
    </head>
    <body>
        <main>
            @if($paid)
                <h1>PAYMENT DONE</h1>
                <a href="/">Go back to App</a>
            @else
                <h1>PAYMENT DUE</h1>
                <p>Pay to the following invoice:</p>
                <pre>{{ $invoiceRequest }}</pre>
                <form action="/lnpay/pay" method="POST">
                    @csrf
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
            @endif
        </main>
    </body>
</html>
