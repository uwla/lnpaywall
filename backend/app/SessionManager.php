<?php

namespace App;
use Illuminate\Support\Facades\Request;

// Manages session information
class SessionManager {

    /**
     * Field name that contains the session expiration time.
     * @var string
     */
    protected const EXPIRE_AT = 'expire_at';

    /**
     * Field name that contains session payment status.
     * @var string
     */
    protected const PAID = 'paid';
  /**
     * Field name that contains session duration.
     * @var string
     */
    protected const PAID_TIME = 'paid_time';

   /**
     * Field value that indicates payment has been confirmed.
     * @var string
     */
    protected const PAID_CONFIRMED = 'y';

   /**
     * Field value that indicates payment is pendent.
     * @var string
     */
    protected const PAID_PENDENT = 'n';

    /**
     * Get session expiration date as UNIX timestamp.
     *
     * @return integer
     */
    public static function getSessionExpireTime() {
        return self::session()->get(self::EXPIRE_AT, 0);
    }

    /**
     * Get session remaining time in seconds.
     *
     * @return integer
     */
    public static function getSessionRemainingTime() {
        $now = time();
        $remaining = $now - self::getSessionExpireTime();
        return max(0, $remaining);
    }

    /**
     * Determines if session has ended.
     *
     * @return boolean
     */
    public static function timeExpired() {
        return time() > self::getSessionExpireTime();
    }

    /**
     * Determines if the payment for the current session has been confirmed.
     *
     * @return boolean
     */
    public static function userHasPaidSession() {
        $payment_status = self::session()->get(self::PAID, self::PAID_PENDENT);
        return $payment_status == self::PAID_CONFIRMED;
    }

    /**
     * Mark current session payment as confirmed.
     *
     * @return void
     */
    public static function markSessionPaymentAsConfirmed() {
        self::session()->put(self::PAID, self::PAID_CONFIRMED);
    }

     /**
     * Set the paid time for the session.
     *
     * @return void
     */
    public static function setSessionPaidTime($time) {
        self::session()->put(self::PAID_TIME, $time);
    }

    /**
     * Start session countdown.
     *
     * @return boolean
     */
    public static function sessionHasStarted() {
        $started_at = self::session()->get(SELF::EXPIRE_AT, -1);
        return $started_at > 0 && $started_at <= time();
    }

    /**
     * Start session countdown.
     *
     * @param   int $time_paid Amount of time paid for the new session.
     * @return void
     */
    public static function startSession() {
        $session = self::session();
        $time_paid = $session->get(self::PAID_TIME, 0);
        $session->put(SELF::EXPIRE_AT, time() + $time_paid);
    }

    /**
     * End current session (will require new payment).
     *
     * @return void
     */
    public static function endSession() {
        self::session()->forget([self::PAID, self::EXPIRE_AT]);
    }

    /**
     * Get the current request session.
     *
     * @return \Illuminate\Contracts\Session\Session
     */
    private static function session() {
        return Request::session();
    }
}
