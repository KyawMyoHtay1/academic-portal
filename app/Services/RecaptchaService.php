<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Verify reCAPTCHA token with Google
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        $secretKey = config('recaptcha.secret_key');
        $scoreThreshold = config('recaptcha.score_threshold', 0.5);

        if (empty($secretKey)) {
            // If no secret key configured, skip verification (for development)
            Log::warning('reCAPTCHA secret key not configured. Skipping verification.');

            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post(config('recaptcha.verify_url'), [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip ?? request()->ip(),
            ]);

            $result = $response->json();

            if (! isset($result['success']) || ! $result['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'errors' => $result['error-codes'] ?? [],
                    'ip' => $ip ?? request()->ip(),
                ]);

                return false;
            }

            // Check score threshold for v3
            $score = $result['score'] ?? 0.0;
            if ($score < $scoreThreshold) {
                Log::warning('reCAPTCHA score below threshold', [
                    'score' => $score,
                    'threshold' => $scoreThreshold,
                    'ip' => $ip ?? request()->ip(),
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'message' => $e->getMessage(),
                'ip' => $ip ?? request()->ip(),
            ]);

            // Fail open in case of network issues (optional - you may want to fail closed)
            return false;
        }
    }
}
