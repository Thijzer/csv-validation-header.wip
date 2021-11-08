<?php

namespace Misery\Component\Common\Cursor;

/**
 * CondensedCursor
 * Allows you to condense the cursor functionally into less lines
 */
class CondensedCursor implements CursorInterface
{
    /** @var CursorInterface */
    private $cursor;
    /** @var int|null */
    private $count;
    /** @var callable */
    private $function;

    public function __construct(CursorInterface $cursor, callable $function)
    {
        $this->cursor = $cursor;
        $this->function = $function;
    }

    /**
     * @inheritDoc
     */
    public function loop(callable $callable): void
    {
        while ($this->valid()) {
            $callable($this->current());
            $this->next();
        }
        $this->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Generator
    {
        while ($this->valid()) {
            yield $this->key() => $this->current();
            $this->next();
        }
        $this->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $function = $this->function;
        $item = $this->cursor->current();
        if ($item === false) {
            return false;
        }
        if ($item = $function($item)) {
            return $item;
        }

        $this->cursor->next();
        return $this->current();
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        $this->cursor->next();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->cursor->key();
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
        if (false === $this->valid()) {
            $this->count = $this->key() - 1;
        }

        $this->cursor->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public function seek($pointer): void
    {
        $this->cursor->seek($pointer);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        if (null === $this->count) {
            $this->loop(static function () {});
        }

        return $this->count;
    }
}