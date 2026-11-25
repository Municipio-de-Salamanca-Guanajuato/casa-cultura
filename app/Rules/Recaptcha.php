<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Throwable;

class Recaptcha implements ValidationRule
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = 'Por favor confirma que no eres un robot.';

        if (empty($value) || !is_string($value)) {
            $fail($message);
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post(self::VERIFY_URL, [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            // Si Google no responde o devuelve error, se bloquea el login
            if (!$response->successful() || $response->json('success') !== true) {
                $fail($message);
            }
        } catch (Throwable $e) {
            report($e);
            $fail($message);
        }
    }
}
