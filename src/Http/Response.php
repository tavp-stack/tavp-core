<?php

declare(strict_types=1);

namespace Tavp\Core\Http;

/**
 * A readable wrapper around the outgoing HTTP response.
 *
 * Controllers return a string, but they can also use this helper to
 * send JSON, set status codes, or redirect.
 */
class Response
{
    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;

        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function json(mixed $data, int $status = 200): self
    {
        $this->content = (string) json_encode($data);
        $this->statusCode = $status;
        $this->headers['Content-Type'] = 'application/json';

        return $this;
    }

    /**
     * Send the response to the client.
     */
    public function send(): string
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        return $this->content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
