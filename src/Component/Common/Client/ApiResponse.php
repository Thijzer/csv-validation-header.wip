<?php

namespace Misery\Component\Common\Client;

class ApiResponse
{
    private $code;
    private $message;
    private $content;

    public function __construct(int $code = null, string $message = null, $content)
    {
        $this->code = $code;
        $this->message = $message;
        $this->content = $content;
    }

    public static function create(array $data, string $code = null): self
    {
        return new self(
            $data['code'] ?? $code,
            $data['message'] ?? null,
            $data);
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
        return $key ? $this->content[$key] ?? null: $this->content;
    }
}