<?php

namespace Misery\Component\Akeneo\Client;

use Misery\Component\Common\Client\ApiClientInterface;
use Misery\Component\Common\Client\ApiEndpointInterface;
use Misery\Component\Common\Client\ApiResponse;
use Misery\Component\Common\Client\Exception\UnauthorizedException;
use Misery\Component\Common\Pipeline\Exception\InvalidItemException;
use Misery\Component\Common\Processor\BatchSizeProcessor;
use Misery\Component\Writer\ItemWriterInterface;

class ApiWriter implements ItemWriterInterface
{
    /** @var ApiClientInterface */
    private $client;
    private $endpoint;
    /** @var string */
    private $method;
    /** @var BatchSizeProcessor */
    private $batch;

    // TODO: add support for batching
    private $pack = [];

    public function __construct(ApiClientInterface $client, ApiEndpointInterface $endpoint, string $method = null)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->batch = new BatchSizeProcessor(10);
    }

    public function write(array $data): void
    {
        if ($data === []) {
            return;
        }

        if ($this->method === 'MULTI_PATCH') {
            if (count($this->pack) < 100) {
                $this->pack[] = $data;
                return;
            }

            if (count($this->pack) === 100) {
                $item = $data;
                $this->batch->next();
                echo $this->batch->hasNewBatchPart() ? $this->batch->getBatchPart().'k':'.';
                $data = $this->pack;
                $this->pack = [];
                $this->pack[] = $item;
            }
        }

        $this->doWrite($data);
    }

    public function close(): void
    {
        if (count($this->pack) <= 100 && $this->pack !== []) {
            $this->doWrite($this->pack);
        }
    }

    private function doWrite(array $data)
    {
        try {
            $response = $this->execute($data);
        } catch (\RuntimeException $e) {
            // do nothing
            throw new InvalidItemException('API Runtime exception', [], $data);
        } catch (UnauthorizedException $e) {
            $this->client->refreshToken();
            $response = $this->execute($data);
        }

        if (!in_array($response->getCode(),  [200, 204])) {
            throw new InvalidItemException('API exception', ['message' => $response->getContent()], $data);
        }

        if ($this->method === 'DELETE') {
            $this->client->log($data['identifier'], $response->getCode(), $response->getContent());
        }
    }

    /**
     * @throws UnauthorizedException
     */
    private function execute(array $data): ApiResponse
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
                    ->patch($this->client->getUrlGenerator()->format($this->endpoint->getSingleEndPoint(), $data), $data)
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
                    ->getResponse();
            default:
                throw new \InvalidArgumentException(sprintf('Method %s is not supported', $this->method));
        }
    }
}
