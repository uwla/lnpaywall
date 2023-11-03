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
}

main {
    min-height: 100vh;
    padding: 64px;
}

.title, .text {
    text-align: center;
}

.time-box {
    padding: 10px;
}

.container {
    margin: 0 auto;
    display: inline-block;
}

.hash-content {
    max-width: 400px;
    border: aliceblue;
    resize: none;
}

#goback {
    color: white;
}

#payreq {
    text-wrap: wrap;
    line-break: anywhere;
    margin: 0;
}

.hash {
    color: #fff;
}

.qr-code {
    margin: 40px 85px;
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

.btn-reload {
    color: aliceblue;
    padding: 15px 20px;
    border: bisque;
    border-radius: 5px;
    background: #932828;
}

.reload-box {
    display: flex;
    justify-content: center;
    margin: 10px;
}

#time {
    margin: 0 42%;
    padding: 8px;
    border-radius: 8px;
}



</style>
