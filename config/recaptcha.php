<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Google reCAPTCHA v3 site key and secret key.
    | Get your keys from: https://www.google.com/recaptcha/admin
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Minimum Score Threshold
    |--------------------------------------------------------------------------
    |
    | reCAPTCHA v3 returns a score (0.0 to 1.0) indicating the likelihood
    | that the request is legitimate. Set the minimum score required to
    | pass validation. Recommended: 0.5
    |
    */

    'score_threshold' => (float) env('RECAPTCHA_SCORE_THRESHOLD', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Verify URL
    |--------------------------------------------------------------------------
    |
    | Google's reCAPTCHA verification endpoint
    |
    */

    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
