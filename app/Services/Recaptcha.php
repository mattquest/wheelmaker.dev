<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Recaptcha
{
    /**
     * Verify a reCAPTCHA v3 token with Google.
     *
     * Returns true when the token verifies with a score above the
     * threshold. When no secret is configured (GOOGLE_RECAPTCHA_SECRET
     * unset), verification is skipped and the call passes — the
     * honeypot field and rate limiting are the active defenses then.
     */
    public function passes(?string $token, ?string $ip = null): bool
    {
        $secret = config('app.recaptcha_secret');

        if (empty($secret)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]
            );
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        $result = $response->json();

        return ($result['success'] ?? false) === true
            && ($result['score'] ?? 0) > 0.3;
    }
}
