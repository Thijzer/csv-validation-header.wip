<?php

namespace Misery\Component\Akeneo\Client;

use Assert\Assert;
use Misery\Component\Common\Client\ApiClient;
use Misery\Component\Common\Client\ApiClientInterface;
use Misery\Component\Common\Client\ApiEndpointInterface;
use Misery\Component\Common\Client\Paginator;
use Misery\Component\Common\Client\InMemoryPaginator;
use Misery\Component\Common\Utils\ValueFormatter;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ReaderInterface;

class ApiReader implements ReaderInterface
{
    private $client;
    private $page;
    private $endpoint;
    private $context;
    private $activeEndpoint;

    public function __construct(
        ApiClientInterface $client,
        ApiEndpointInterface $endpoint,
        array $context
    ) {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->context = $context;
    }

    private function request($endpoint = false): array
    {
        if (!$endpoint) {
            $endpoint = $this->endpoint->getAll();
        }

        // todo - create function for this. Check how filtering must be applied.
        if(isset($this->context['limiters']['query_array'])) {
            $endpoint = $this->client->getUrlGenerator()->generate($endpoint);
            $endpoint = sprintf('%s?search=%s', $endpoint, json_encode($this->context['limiters']['query_array']));
            $endpoint = str_replace('"', '%22', $endpoint);

            $items = $this->client
                ->get($endpoint, [])
                ->getResponse()
                ->getContent();

            return $items;
        }

        if(isset($this->context['limiters']['querystring'])) {
            $querystring = ValueFormatter::format($this->context['limiters']['querystring'], $this->context);
            $endpoint = sprintf($querystring, $endpoint);
        }

        $items = [];
        if (isset($this->context['filters']) && !empty($this->context['filters'])) {
            $endpoint = sprintf('%s?search=', $endpoint);
            foreach ($this->context['filters'] as $attrCode => $filterValues) {
                $valueChunks = array_chunk(array_values($filterValues),100);
                foreach ($valueChunks as $filterChunk) {
                    $filter = [$attrCode => [['operator' => 'IN', 'value' => $filterChunk]]];
                    $chunkEndpoint = sprintf('%s%s&limit=100', $endpoint, json_encode($filter));

                    $result = $this->client
                        ->get($this->client->getUrlGenerator()->generate($chunkEndpoint))
                        ->getResponse()
                        ->getContent();

                    if (empty($items)){
                        $items = $result;

                        continue;
                    }

                    $items['_embedded']['items'] = array_merge(
                        $items['_embedded']['items'],
                        $result['_embedded']['items']
                    );
                }
            }

            return $items;
        }

        $items = $this->client
            ->get($this->client->getUrlGenerator()->generate($endpoint))
            ->getResponse()
            ->getContent();

        // when supplying a container we jump inside that container to find loopable items
        if ($this->context['container']) {
            if (array_key_exists($this->context['container'], $items)) {
                $items['_embedded']['items'] = $items[$this->context['container']];
                unset($items[$this->context['container']]);
            }
        }

        return $items;
    }

    public function read()
    {
        if (isset($this->context['multiple'])) {
           return $this->readMultiple();
        }

        if (null === $this->page) {
            $this->page = Paginator::create($this->client, $this->request());
        }

        $item = $this->page->getItems()->current();
        if (!$item) {
            $this->page = $this->page->getNextPage();
            if (!$this->page) {
                return false;
            }
            $item = $this->page->getItems()->current();
        }
        $this->page->getItems()->next();

        unset($item['_links']);

        return $item;
    }

    public function readMultiple()
    {
        foreach ($this->context['list'] as $key => $endpointItem) {
            if ($this->activeEndpoint !== $endpointItem || null === $this->page) {
                $endpoint = sprintf($this->endpoint->getAll(), $endpointItem);
                $this->page = Paginator::create($this->client, $this->request($endpoint));
                $this->activeEndpoint = $endpointItem;
            }

            $item = $this->page->getItems()->current();
            if (!$item) {
                $this->page = $this->page->getNextPage();
                if (!$this->page) {
                    unset($this->context['list'][$key]);

                    return $this->readMultiple();
                }
                $item = $this->page->getItems()->current();
            }

            $this->page->getItems()->next();
            if (!$item) {
                unset($this->context['list'][$key]);

                return $this->readMultiple();
            }

            unset($item['_links']);

            return $item;
        }

        return false;
    }

    public function getIterator(): \Iterator
    {
        while ($item = $this->read()) {
            yield $item;
        }
    }

    public function find(array $constraints): ReaderInterface
    {
        // TODO we need to implement a find or search int the API
        $reader = $this;
        foreach ($constraints as $columnName => $rowValue) {
            if (is_string($rowValue)) {
                $rowValue = [$rowValue];
            }

            $reader = $reader->filter(static function ($row) use ($rowValue, $columnName) {
                return in_array($row[$columnName], $rowValue);
            });
        }

        return $reader;
    }

    public function filter(callable $callable): ReaderInterface
    {
        return new ItemReader($this->processFilter($callable));
    }

    private function processFilter(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            if (true === $callable($row)) {
                yield $key => $row;
            }
        }
    }

    public function map(callable $callable): ReaderInterface
    {
        return new ItemReader($this->processMap($callable));
    }

    private function processMap(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            yield $key => $callable($row);
        }
    }

    public function getItems(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function clear(): void
    {
        // TODO: Implement clear() method.
    }
}
