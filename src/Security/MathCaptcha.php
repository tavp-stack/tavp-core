<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

class MathCaptcha implements CaptchaInterface
{
    private const TTL = 120;

    public function generate(): array
    {
        // Default: simple addition of two small numbers so everyone can solve
        // it. Operator set and operand range are configurable for stricter
        // deployments (e.g. config('captcha.math.max')).
        $max = (int) $this->opt('captcha.math.max', 10);
        $allowMinus = (bool) $this->opt('captcha.math.allow_minus', false);
        $allowMultiply = (bool) $this->opt('captcha.math.allow_multiply', false);

        $a = random_int(1, max(1, $max));
        $b = random_int(1, max(1, $max));

        $op = '+';
        if ($allowMultiply || $allowMinus) {
            $ops = ['+'];
            if ($allowMinus) {
                $ops[] = '-';
            }
            if ($allowMultiply) {
                $ops[] = '×';
            }
            $op = $ops[array_rand($ops)];
        }

        $answer = match ($op) {
            '+' => $a + $b,
            '-' => max($a, $b) - min($a, $b),
            '×' => $a * $b,
            default => 0,
        };

        $token = bin2hex(random_bytes(16));

        $_SESSION['_captcha'][$token] = [
            'answer' => (string) $answer,
            'type' => 'math',
            'expires_at' => time() + self::TTL,
        ];

        return [
            'token' => $token,
            'question' => "Berapa {$a} {$op} {$b}?",
            'type' => 'math',
        ];
    }

    public function verify(string|int $answer, string $token): bool
    {
        $this->cleanup();

        if (!isset($_SESSION['_captcha'][$token])) {
            return false;
        }

        $data = $_SESSION['_captcha'][$token];

        if ($data['type'] !== 'math' || time() > $data['expires_at']) {
            unset($_SESSION['_captcha'][$token]);
            return false;
        }

        $valid = hash_equals($data['answer'], trim($answer));

        unset($_SESSION['_captcha'][$token]);

        return $valid;
    }

    private function cleanup(): void
    {
        if (!isset($_SESSION['_captcha']) || !is_array($_SESSION['_captcha'])) {
            return;
        }

        $now = time();

        foreach ($_SESSION['_captcha'] as $token => $data) {
            if ($now > ($data['expires_at'] ?? 0)) {
                unset($_SESSION['_captcha'][$token]);
            }
        }
    }

    private function opt(string $key, mixed $default): mixed
    {
        if (function_exists('config')) {
            return config($key, $default);
        }
        return $default;
    }
}
