<?php

namespace Misery\Component\Reader;

class ItemReader implements ItemReaderInterface
{
    private $cursor;

    public function __construct(\Iterator $cursor)
    {
        $this->cursor = $cursor;
    }

    /** @inheritDoc */
    public function read()
    {
        $item = $this->cursor->current();
        $this->cursor->next();

        return $item;
    }

    public function index(array $lines): ItemReaderInterface
    {
        return new self($this->processIndex($lines));
    }

    private function processIndex(array $lines): \Generator
    {
        foreach ($lines as $lineNr) {
            $this->seek($lineNr);
            yield $lineNr => $this->cursor->current();
        }
    }

    /**
     * Adds seek support for \Iterator objects
     */
    public function seek($pointer): void
    {
        $this->cursor->rewind();
        while ($this->cursor->valid()) {
            if ($this->cursor->key() === $pointer) {
                break;
            }
            $this->cursor->next();
        }

        // @TODO throw outofboudexception
    }

    public function find(array $constraints): ReaderInterface
    {
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
