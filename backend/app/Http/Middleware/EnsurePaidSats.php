<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// This middleware ensures the user has paid satoshis before accessing the requested resource.
class EnsurePaidSats
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user has paid, allow him to proceed.
        if ($this->hasPaid($request))
            return $next($request);

        // Otherwise, redirect him to payment page.
        return redirect()->route('lnpay.pay');
    }

    /**
     * Determine whether the user making the request has paid to access the web service.
     *
     * @param  \Illuminate\Http\Request
     * @return boolean
     */
    public function hasPaid(Request $request)
    {
        // Get the session associated with the request.
        $session = $request->session();

        // has not paid
        if ('y' != $session->get('paid', 'n'))
        {
            return false;
        }

        // Payment done and this is the first access.
        // So, mark the beginning of this session.
        if (! $session->has('started_at'))
        {
            $session->put('started_at', time());
        }

        // Payment done and already accessed before.
        // Needs to verify if there is time remaining.
        $start = $session->get('started_at', 0);
        $time = $session->get('time_paid', 0);
        if (time() > $start+$time)
        {
            // Time has expired. Delete session information.
            $session->forget('started_at');
            $session->forget('time_paid');
            $session->forget('paid');
            return false;
        }

        // Payment done and time has not expired.
        return true;
    }
}
