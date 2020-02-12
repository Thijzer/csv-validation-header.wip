<?php

namespace Misery\Component\Reader;

class ItemReader implements ItemReaderInterface
{
    private $cursor;

    public function __construct(\Iterator $cursor)
    {
        $this->cursor = $cursor;
    }

    public function read(): \Iterator
    {
        return $this->cursor;
    }

    public function index(array $lines): ItemReaderInterface
    {
        $items = [];
        foreach ($lines as $lineNr) {
            $this->cursor->seek($lineNr);
            $items[$lineNr] = $this->cursor->current();
        }

        $this->cursor->rewind();

        return new self(new ItemCollection($items));
    }

    public function find(array $constraints): ReaderInterface
    {
        $reader = $this;
        foreach ($constraints as $columnName => $rowValue) {
            $reader = $reader->filter(static function ($row) use ($rowValue, $columnName) {
                return $row[$columnName] === $rowValue;
            });
        }

        return $reader;
    }

    public function filter(callable $callable): ReaderInterface
    {
        return new self($this->processFilter($callable));
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
        return new self($this->processMap($callable));
    }

    private function processMap(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            yield $key => $callable($row);
        }
    }

    public function getIterator(): \Iterator
    {
        return $this->cursor;
    }

    public function getItems(): array
    {
        return iterator_to_array($this->cursor);
    }
}
