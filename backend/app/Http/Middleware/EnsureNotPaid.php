<?php

namespace App\Http\Middleware;

use App\Http\Controllers\HttpProxyController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// This middleware is used to handle access to payment pages,
// ensuring the user will only see them if he has not paid yet.
class EnsureNotPaid extends EnsurePaidSats
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user has not made a payment, we allow him to proceed to the payment page.
        if (! $this->hasPaid($request))
            return $next($request);

        // Otherwise, we do not show the payment page.
        // Instead, we proxy the request to the FRONTEND and return back the response.
        return (new HttpProxyController)->redirect();
    }
}
