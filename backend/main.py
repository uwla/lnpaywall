from http.server import BaseHTTPRequestHandler, HTTPServer
import http.cookies

class MyServer(BaseHTTPRequestHandler):
    def do_GET(self):
        if   self.path == '/lnpay/auth':
            self.auth()
        elif self.path == '/lnpay/pay':
            self.pay()
        elif self.path == '/lnpay/paid':
            self.paid()
        elif self.path == '/lnpay/expired':
            self.expired()
        else:
            self.send_error(403)

    def auth(self):


        # get cookies from header
        cookies = self.headers.get('Cookie')

        if cookies:
            # Parse the cookie header into a dictionary
            cookie_dict = http.cookies.SimpleCookie(cookies)

            # Get the value of a specific cookie
            if cookie_dict['_auth_']:
                if cookie_dict['_auth_'].value == 'yes':
                    self.send_response(200)
                    self.send_header("Set-Cookie", "_auth_=yes")
                    self.end_headers()
                    return

        self.send_response(401)
        self.send_header('WWW-Authenticate', 'Basic realm="Login"')
        self.end_headers()
        return

    def pay(self):
        html = """
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                <h1>Please, pay to proceed</h1>
                <a href="/"><button>PAY</button></a>
            </body>
        </html>
        """
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.send_header("Set-Cookie", "_auth_=yes")
        self.end_headers()
        self.wfile.write(bytes(html, "utf-8"))

    def paid(self):
        html = """
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                <h1>PAYMENT SUCCESSFUL</h1>
                <a href="/"><button>GO TO MAIN APP</button></a>
            </body>
        </html>
        """
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.send_header("Set-Cookie", "_auth_=yes")
        self.end_headers()
        self.wfile.write(bytes(html, "utf-8"))

    def expired(self):
        html = """
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                <h1>PAYMENT EXPIRED</h1>
                <a href="/lnpay/pay"><button>PAY AGAIN</button></a>
            </body>
        </html>
        """
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.send_header("Set-Cookie", "_auth_=no")
        self.end_headers()
        self.wfile.write(bytes(html, "utf-8"))


if __name__ == '__main__':
    server_address = ('', 3000)
    httpd = HTTPServer(server_address, MyServer)
    print('Server running at http://localhost:3000')
    httpd.serve_forever()
