<style>
* {
    box-sizing: border-box;
    font-family: Inter,Roobert,Helvetica Neue,Helvetica,Arial,sans-serif;
}

html, body {
    margin: 0;
    padding: 0;
    width: 100%;
}

body {
    display: flex;
    background: linear-gradient(#151c2e, #1b1f29);
    color: #fff;
    padding: 24px 16px;
}

main {
    min-height: 100vh;
    margin: 0 auto;
}

h1, p {
    text-align: center;
}

#time-box {
    margin-top: 3em;
    margin-bottom: 3em;
}

.hash-content {
    max-width: 400px;
    border: aliceblue;
    resize: none;
    margin: 0 auto;
}

#go-back {
    color: white;
}

#pay-req {
    text-wrap: wrap;
    line-break: anywhere;
    white-space: pre-wrap;
    margin: 0;
}

.hash {
    color: #fff;
}

#qrCode svg {
    display: block;
    margin: 0 auto;
}

button {
    cursor: pointer;
}

.copy-hash {
    color: aliceblue;
    background: #2db35d;
    border: aliceblue;
    cursor: pointer;
    padding: 5px 15px;
    margin-left: auto;
    position: relative;
    display: block;
    width: fit-content;
    border-radius: 8px;
    top: -1em;
}

.copied {
    display: none;
    color: grey;
    text-align: right;
    font-size: 75%;
    margin-top: -8px;
    font-weight: 800;
    position: absolute;
    margin-left: 350px;
}

button:focus-within + .copied {
    display: block;
}

#btn-new-invoice, #btn-confirm-payment {
    color: aliceblue;
    padding: 15px 20px;
    border: bisque;
    border-radius: 5px;
    background: #932828;
    margin: 1.5em auto;
    display: block
}

#time-input {
    display: block;
    margin: 0 auto;
    padding: 8px;
    border-radius: 8px;
    min-width: 200px;
}

</style>
