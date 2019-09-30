<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;

class ItemCollection implements CsvInterface, CursorInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $position = 0;
    private $items;
    private $keys;

    public function __construct(array $items = [])
    {
        // array_values removes any position keys
        $this->items = $items;
        $this->keys = array_keys($items);
    }

    public function add($value)
    {
        $this->items[] = $value;
    }

    public function set($key, $value): void
    {
        $this->items[$key] = $value;
        $this->keys[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function loop(callable $callable): void
    {
        foreach ($this->getIterator() as $row) {
            $callable($row);
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
    public function getHeaders(): array
    {
        return array_keys($this->current());
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeaders(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->items) ?? $this->valid();
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        ++$this->position;
        next($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->keys[$this->position] ?? $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return $this->position < $this->count();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        reset($this->items);
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function seek($pointer): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($this->key() === $pointer) {
                break;
            }
            $this->next();
        }

        if (!$this->valid()) {
            //throw new OutOfBoundsException('Invalid position');
        }
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->items;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->items);
    }
}