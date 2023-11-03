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
    overflow: hidden;
    resize: none;
}

.hash {
    color: #fff;
}

.qr-code {
    margin: 10px 85px;
}

.copy-hash {
    color: aliceblue;
    background: #2db35d;
    border: aliceblue;
    cursor: pointer;
    padding: 5px 15px;
    float: right;
    margin-bottom: -34px;
    position: relative;
    border-top-left-radius: 10px;
    border-bottom-right-radius: 10px;
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
    margin: 20px;
}

#time {
    margin: 0 42%;
}


</style>
