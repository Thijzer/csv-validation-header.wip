<?php

namespace Misery\Component\Akeneo\Client;

use Misery\Component\Common\Client\ApiClient;
use Misery\Component\Common\Client\ApiEndpointInterface;
use Misery\Component\Common\Client\ApiResponse;
use Misery\Component\Common\Client\Exception\UnauthorizedException;
use Misery\Component\Writer\ItemWriterInterface;

class ApiWriter implements ItemWriterInterface
{
    /** @var ApiClient */
    private $client;
    private $endpoint;
    /** @var string */
    private $method;

    // TODO: add support for batching
    private $pack = [];

    public function __construct(ApiClient $client, ApiEndpointInterface $endpoint, string $method = null)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    public function write(array $data): void
    {
        if ($this->method === 'MULTI_PATCH') {
            if (count($this->pack) < 100) {
                $this->pack[] = $data;
                return;
            }
            if (count($this->pack) === 100) {
                $data = $this->pack;
                $this->pack = [];
            }
        }
        try {
            $response = $this->doWrite($data);
        } catch (UnauthorizedException $e) {
            $this->client->refreshToken();
            $response = $this->doWrite($data);
        }

        if ($this->method === 'DELETE') {
            $this->client->log($data['identifier'], $response->getCode(), $response->getContent());
        }
    }

    /**
     * @throws UnauthorizedException
     */
    private function doWrite(array $data): ApiResponse
    {
        switch ($this->method) {
            case 'DELETE':
            case 'delete':
                return $this->client
                    ->delete($this->client->getUrlGenerator()->generate($this->endpoint->getSingleEndPoint(), $data['identifier']))
                    ->getResponse()
                ;
            case 'PATCH':
            case 'patch':
                return $this->client
                    ->patch($this->client->getUrlGenerator()->generate($this->endpoint->getSingleEndPoint(), $data['identifier']), $data)
                    ->getResponse()
                ;
            case 'MULTI_PATCH':
            case 'multi_patch':
                return $this->client
                    ->multiPatch($this->client->getUrlGenerator()->generate($this->endpoint->getAll()), $data)
                    ->getResponse()
                    ;
            case 'POST':
            case 'post':
                return $this->client
                    ->post($this->client->getUrlGenerator()->generate($this->endpoint->getAll()), $data)
                    ->getResponse()
                ;
//            case 'PUT':
//            case 'put':
//                return $this->client
//                    ->put($this->client->getUrlGenerator()->generate($this->endpoint->getSingleEndPoint()), $data['identifier'])
//                    ->getResponse()
//                ;
            default:
                throw new \InvalidArgumentException(sprintf('Method %s is not supported', $this->method));
        }
    }
}
