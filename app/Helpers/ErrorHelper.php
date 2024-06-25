<?php

// app/Helpers/ErrorHelper.php

namespace App\Helpers;

class ErrorHelper
{
    public static function otpVerificationFailed()
    {
        return 'Invalid or expired OTP.';
    }

    public static function paymentProcessingFailed()
    {
        return 'Payment processing failed.';
    }

    public static function genericError()
    {
        return 'An error occurred during payment processing.';
    }
}
