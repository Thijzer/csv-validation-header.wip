<?php

namespace Misery\Component\Common\Cursor;

class FunctionalCursor implements CursorInterface
{
    /** @var CursorInterface */
    private $cursor;
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
        return $function($this->cursor->current());
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
        return $this->cursor->valid();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
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
        return $this->cursor->count();
    }
}