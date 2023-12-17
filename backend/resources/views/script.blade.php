<script>
    const invoiceId = "{{ $invoiceId }}"
    const invoiceRequest = "{{ $invoiceRequest }}"
    const csrf  = "{{ csrf_token() }}"

    setInterval(() => {
        fetch('/lnpay/confirm', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ invoiceId, invoiceRequest })
        })
        .then(response => response.json())
        .then(data => {
            if (data.confirmed === true)
                document.querySelector("#confirm-payment").submit()
        })
        .catch(error => {
            console.log(error.response)
        });
    }, 3000);


    function copyToClipboard() {
        const textToCopy = document.querySelector('#pay-req').innerText;
        const temp = document.createElement('textarea');
        temp.value = textToCopy;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
    }

    document.querySelector('.copy-hash').addEventListener('click', copyToClipboard);
</script>
