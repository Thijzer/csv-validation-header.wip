<?php

namespace Misery\Component\Common\Cursor;

class CachedCursor implements CursorInterface
{
    const SMALL_CACHE_SIZE = 1000;
    const MEDIUM_CACHE_SIZE = 5000;
    const LARGE_CACHE_SIZE = 10000;

    private $position = 0;
    private $cursor;
    private $items = [];

    private $options = [
        'cache_size' => self::MEDIUM_CACHE_SIZE,
    ];
    private $range = [];

    public function __construct(CursorInterface $cursor, array $options = [])
    {
        $this->cursor = $cursor;
        $this->options = array_merge($this->options, $options);
        $this->position = $cursor->key();
    }

    public static function create(CursorInterface $cursor, array $options = []): self
    {
        return new self($cursor, $options);
    }

    public function loop(callable $callable): void
    {
        while ($this->valid()) {
            $callable($this->current());
            $this->next();
        }
        $this->rewind();
    }

    public function getItemKeys(): array
    {
        return array_keys($this->current());
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $this->prefetch($this->position);

        return $this->items[$this->position] ?? false;
    }

    /**
     * Cursor is not rewind
     * so it could keep fetches in chuncks without reset
     * @param int $i position
     */
    private function prefetch(int $i): void
    {
        if (!isset($this->range[$i])) {
            $this->range = array_flip(range($i,$i + $this->options['cache_size']-1));
            $this->items = [];
            while ($row = $this->cursor->current()) {
                if (isset($this->range[$this->cursor->key()])) {
                    $this->items[$this->cursor->key()] = $row;
                    if ($this->count() === $this->options['cache_size']) {
                        break;
                    }
                }
                $this->cursor->next();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->cursor->rewind();
        $this->position = $this->cursor->key();
    }

    /**
     * {@inheritDoc}
     */
    public function seek($pointer): void
    {
        $this->position = (int) $pointer;
        if (!$this->valid()) {
            //throw new OutOfBoundsException('Invalid position');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->cursor->count();
    }

    public function clear(): void
    {
        $this->items = [];
    }
}