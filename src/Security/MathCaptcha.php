<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

class MathCaptcha implements CaptchaInterface
{
    private const TTL = 120;

    private const OPERATORS = ['+', '-', '×'];

    public function generate(): array
    {
        $a = random_int(1, 20);
        $b = random_int(1, 20);
        $op = self::OPERATORS[array_rand(self::OPERATORS)];

        $answer = match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
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
}
