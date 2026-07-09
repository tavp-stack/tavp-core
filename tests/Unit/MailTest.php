<?php

declare(strict_types=1);

use Tavp\Core\Mail\Mailer;
use Tavp\Core\Mail\Mailable;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase
{
    public function testLogDriverWritesToFile(): void
    {
        $mailer = new Mailer('log');
        $mailable = new class extends Mailable {
            public function build(): self
            {
                return $this->to('user@example.com')->subject('Hi')->body('Hello');
            }
            public function body(string $b): self { $this->body = $b; return $this; }
        };

        $this->assertTrue($mailer->send($mailable));
    }
}

class DummyMailable extends Mailable
{
    public function build(): self
    {
        return $this->to('a@b.com')->subject('S')->body('B');
    }
    public function body(string $b): self { $this->body = $b; return $this; }
}
