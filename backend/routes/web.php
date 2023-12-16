<?php

use App\Http\Middleware\EnsurePaidSats;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client as HttpClient;

use App\Http\Controllers\LNPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/lnpay/pay', [LNPaymentController::class, 'pay'])->name('lnpay.pay');
Route::post('/lnpay/pay', [LNPaymentController::class, 'pay']);
Route::post('/lnpay/confirm', [LNPaymentController::class, 'confirmPayment']);
Route::post('/lnpay/confirmJSON', [LNPaymentController::class, 'confirmPaymentJSON']);

// simple helper function to filter header array on request & response
function filter_headers($headers)
{
    // currently, we will only allow these two headers to be passed for safety reasons
    $allowed_headers = ['accept', 'content-type'];

    $new_headers = [];
    foreach ($headers as $key => $value)
        if (in_array(strtolower($key), $allowed_headers))
            $new_headers[$key] = $value;

    return $new_headers;
}

Route::middleware(EnsurePaidSats::class)->any('/{path}', function() {
    $base_uri = env('FRONTEND_URL', 'http://localhost');

    // create http client
    $client = new GuzzleHttp\Client([
        'base_uri'    => $base_uri,
        'http_errors' => false, // disable guzzle exception on 4xx or 5xx response code
        'timeout'     => 20.0,
    ]);

    // extract request information to be passed to the proxy
    $path = Request::path();
    $method = RequesT::method();
    $headers = filter_headers(Request::header());
    $query = Request::getQueryString();
    $body = Request::getContent();

    // perform a request acting as a proxy
    $response = $client->request($method, $path, [
        'headers' => $headers,
        'query'   => $query,
        'body'    => $body,
    ]);

    // extract information
    $content = $response->getBody()->getContents();
    $status_code = $response->getStatusCode();
    $headers = filter_headers($response->getHeaders());

    // return the request
    return response($content, $status_code)->withHeaders($headers);

})->where('path', '.*'); // required to allow $path to catch all sub-path
