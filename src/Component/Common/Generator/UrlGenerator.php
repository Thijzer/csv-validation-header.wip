<?php

namespace Misery\Component\Common\Generator;

class UrlGenerator implements GeneratorInterface
{
    private $url;

    public function __construct(string $domain)
    {
        $this->url = rtrim($domain, DIRECTORY_SEPARATOR);
    }

    public function append(string $endpoint): void
    {
        $this->url .= DIRECTORY_SEPARATOR.\ltrim($endpoint, DIRECTORY_SEPARATOR);
    }

    public function generate(...$data): string
    {
        foreach ($data as $part) {
            if (is_string($part)) {
                $this->append($part);
            }
            if (is_array($part)) {
                return $this->createParams($this->url, $part);
            }
        }

        return $this->url;
    }

    private function createParams(string $endpoint, array $params): string
    {
        return $params !== [] ? $endpoint . '?' . \http_build_query($params) : $endpoint;
    }
}