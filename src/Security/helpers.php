<?php

declare(strict_types=1);

use Tavp\Core\Security\CaptchaManager;

if (!function_exists('captcha')) {
    function captcha(?string $type = null, array $attributes = []): string
    {
        $type ??= config('captcha.type', 'math');

        return (new CaptchaManager())->render($type, $attributes);
    }
}

if (!function_exists('captcha_verify')) {
    function captcha_verify(?string $answer = null, ?string $token = null): bool
    {
        $answer ??= $_POST['captcha_answer'] ?? '';
        $token ??= $_POST['captcha_token'] ?? '';

        return (new CaptchaManager())->verify($answer, $token);
    }
}
