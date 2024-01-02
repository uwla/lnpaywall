<?php

namespace App\Http\Middleware;

use App\SessionManager;
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
        if ($this->hasPaid())
            return $next($request);

        // Otherwise, redirect him to payment page.
        return redirect()->route('lnpay.pay');
    }

    /**
     * Determine whether the user making the request has paid to access the web service.
     *
     * @return boolean
     */
    public function hasPaid()
    {
        // has not paid
        if (! SessionManager::userHasPaidSession())
            return false;

        // Payment done and this is the first access.
        // So, mark the beginning of this session.
        if (! SessionManager::sessionHasStarted())
            SessionManager::startSession();

        if (SessionManager::timeExpired())
        {
            SessionManager::endSession();
            return false;
        }

        // Payment done and time has not expired.
        return true;
    }
}
