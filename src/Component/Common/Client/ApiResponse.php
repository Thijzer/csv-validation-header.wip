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

    public static function createFromMulti(array $data): self
    {
        $data = array_filter($data, function ($line) {
            return isset($line['message']) && !in_array($line['status_code'], [204, 200]);
        });

        if (count($data) > 0) {
            $line = current($data);
            return new self(
                $line['status_code'],
                $line['message'] ?? null,
                $data);
        }

        return new self(204, null, $data);
    }

    public static function create(array $data = [], string $code = null): self
    {
        return new self(
            $data['status_code'] ?? $code,
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
        return $key ? $this->content[$key] ?? null: $this->content;
    }
}