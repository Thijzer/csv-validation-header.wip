<?php

namespace Misery\Component\Common\Client;

use Misery\Component\Reader\ItemCollection;

class Paginator
{
    private $client;
    private $first;
    private $previous;
    private $next;
    private $count;
    private $items;

    public function __construct(
        ApiClient $client,
        ItemCollection $items,
        string $first = null,
        string $previous = null,
        string $next = null,
        int $count = null
    ) {
        $this->client = $client;
        $this->first = $first;
        $this->previous = $previous;
        $this->next = $next;
        $this->count = $count;
        $this->items = $items;
    }

    public static function create(ApiClient $client, array $data): self
    {
        return new self(
            $client,
            new ItemCollection($data['_embedded']['items'] ?? []),
            $data['_links']['first']['href'] ?? null,
            $data['_links']['previous']['href'] ?? null,
            $data['_links']['next']['href'] ?? null,
            $data['items_count'] ?? null
        );
    }

    /**
     * Returns the page given a complete uri.
     */
    protected function getPage(string $uri): self
    {
        $data = $this->client->get($uri)->getResponse()->getContent();

        return self::create($this->client, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPage(): self
    {
        return $this->getPage($this->first);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage(): ?self
    {
        return $this->hasPreviousPage() ? $this->getPage($this->previous) : null;
    }

    public function getNextPage(): ?self
    {
        return $this->hasNextPage() ? $this->getPage($this->next) : null;
    }

    public function getClient(): ApiClient
    {
        return $this->client;
    }

    public function getFirst(): ?string
    {
        return $this->first;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function getCount(): ?string
    {
        return $this->count;
    }

    public function getItems(): ItemCollection
    {
        return $this->items;
    }

    public function hasNextPage(): bool
    {
        return null !== $this->next;
    }

    public function hasPreviousPage(): bool
    {
        return null !== $this->previous;
    }
}