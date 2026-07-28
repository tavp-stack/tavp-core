<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

interface CaptchaInterface
{
    public function generate(): array;

    public function verify(string|int $answer, string $token): bool;
}
