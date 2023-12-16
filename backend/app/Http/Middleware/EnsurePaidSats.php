<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaidSats
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->hasPaid($request))
            return $next($request);
        return redirect()->route('lnpay.pay');
    }

    public function hasPaid(Request $request)
    {
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
        $time = $session->get('timepaid', 0);
        if (time() > $start+$time)
        {
            // Time has expired. Delete session information.
            $session->forget('started_at');
            $session->forget('timepaid');
            $session->forget('paid');
            return false;
        }

        // Payment done and time has not expired.
        return true;
    }
}
