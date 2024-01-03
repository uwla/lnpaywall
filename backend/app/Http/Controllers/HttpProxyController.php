<?php

namespace App\Http\Controllers;

use App\SessionManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use GuzzleHttp\Client as HttpCLient;

class HttpProxyController extends Controller
{
    // proxy current request to the FRONTEND
    public function proxy()
    {
        $base_uri = env('FRONTEND_URI', 'http://localhost');

        // create http client
        $client = new HttpCLient([
            'base_uri' => $base_uri,
            'http_errors' => false, // disable guzzle exception on 4xx or 5xx response code
            'timeout' => 20.0,
        ]);

        // extract request information to be passed to the proxy
        $path = Request::path();
        $method = RequesT::method();
        $headers = $this->filter_headers(Request::header());
        $query = Request::getQueryString();
        $body = Request::getContent();

        // perform a request acting as a proxy
        $response = $client->request($method, $path, [
            'headers' => $headers,
            'query' => $query,
            'body' => $body,
        ]);

        // extract information
        $content = $response->getBody()->getContents();
        $status_code = $response->getStatusCode();
        $headers = $this->filter_headers($response->getHeaders());

        // get remaining seconds for current session
        $remaining_seconds = SessionManager::getSessionRemainingTime();

        // create a Carbon date for the expiration date of this session
        $expire_date = Carbon::now()->addSeconds($remaining_seconds);

        // add expiration headers to the HTTP response
        $headers['Cache-Control'] = "max-age,{$remaining_seconds}";
        $headers['Expire'] = $expire_date->toRfc1123String();

        // get the content type
        $content_type = $headers['Content-Type'] ??  $headers['content-type'] ?? '';

        // if content type is not empty, it could be an array like this:
        // ['text/html', 'charset=utf8']
        // we need the first item.
        if (is_array($content_type))
            $content_type = implode(';', $content_type);

        // add refresh meta tag
        if (str_starts_with($content_type, 'text/html')) {
            $meta_tag = "<meta http-equiv=\"refresh\" content=\"{$remaining_seconds};url='/lnpay/pay'\" />";
            // append the meta tag right after the head
            $replacement = '<head>' . "\n" . $meta_tag . "\n";
            $content = preg_replace('/<head>/', $replacement, $content, 1);
        }

        // return the request
        return response($content, $status_code)->withHeaders($headers);
    }

    // simple helper function to filter header array on request & response
    public function filter_headers($headers)
    {
        // currently, we will only allow these two headers to be passed for safety reasons
        $allowed_headers = ['accept', 'content-type'];

        $new_headers = [];
        foreach ($headers as $key => $value)
            if (in_array(strtolower($key), $allowed_headers))
                $new_headers[$key] = $value;
        return $new_headers;
    }
}
