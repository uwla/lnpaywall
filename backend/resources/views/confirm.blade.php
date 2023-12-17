<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LNPAYWALl</title>
    </head>
    <body>
        @include('style')
        <main>
            @if($paid)
                <h1>PAYMENT DONE</h1>
                <a id="go-back"  href="/">Go back to App</a>
                <script>
                    setInterval(() => window.location.href = "/", 3000);
                </script>
            @else
                <h1>PAYMENT NOT SUCCESSFUL</h1>
                <form action="/lnpay/pay" method="POST">
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
                    <button>GO BACK TO PAYMENT PAGE</button>
                </form>
            @endif
        </main>
    </body>
</html>
