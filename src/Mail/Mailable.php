<?php

declare(strict_types=1);

namespace Tavp\Core\Mail;

/**
 * Base class for emails. Subclasses set recipient, subject and body.
 */
abstract class Mailable
{
    protected string $to = '';
    protected string $subject = '';
    protected string $body = '';

    abstract public function build(): self;

    public function to(string $address): self
    {
        $this->to = $address;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function render(): string
    {
        return $this->body;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}
