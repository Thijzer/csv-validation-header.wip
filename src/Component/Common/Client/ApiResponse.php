<?php

namespace Misery\Component\Common\Client;

class ApiResponse
{
    private $code;
    private $message;
    private $content;

    private function __construct(int $code = null, string $message = null, $content)
    {
        $this->code = $code;
        $this->message = $message;
        $this->content = $content;
    }

    public static function create(array $data = [], int $code = null): self
    {
        return new self(
            $code,
            $data['message'] ?? null,
            $data
        );
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $key
     *
     * @return mixed|null
     */
    public function getContent(string $key = null)
    {
        return $key ? $this->content[$key] ?? $this->getContentByKeys(...explode('.', $key)) : $this->content;
    }

    private function getContentByKeys(...$keys)
    {
        $content = $this->content;
        foreach ($keys as $key) {
            $content = $content[$key] ?? null;
        }

        return $content;
    }

    public function isSuccessful(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }
}
