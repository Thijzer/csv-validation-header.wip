<?php

namespace Misery\Component\Common\Cursor;

class CachedCursor implements CursorInterface
{
    const MED_CACHE_SIZE = 10000;

    private $position = 0;
    private $cursor;
    private $items;

    private $options = [
        'cache_size' => self::MED_CACHE_SIZE,
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
        while ($row = $this->current()) {
            $callable($row);
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
        $i = $this->position;

        if (!in_array($i, $this->range)) {
            $this->range = range($i,$i+$this->options['cache_size']);
            $this->cursor->loop(function (array $row) {
                if (\in_array($this->cursor->key(), $this->range, true)) {
                    $this->items[$this->cursor->key()] = $row;
                }
            });
        }

        return $this->items[$i] ?? false;
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
        return \count($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }
}