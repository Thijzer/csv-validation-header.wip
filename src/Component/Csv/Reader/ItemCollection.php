<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Csv\Exception\InvalidCsvElementSizeException;

class ItemCollection implements CursorInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $position = 0;
    private $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function loop(callable $callable): void
    {
        while ($this->valid()) {
            $callable($this->current());
            $this->next();
        }
        $this->rewind();
    }

    public function getHeaders(): array
    {
        return array_keys($this->current());
    }

    public function hasHeaders(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->items[$this->position];
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
        return isset($this->items[$this->position]);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->position = 0;
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
}