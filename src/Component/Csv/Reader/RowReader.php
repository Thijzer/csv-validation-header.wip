<?php

namespace Misery\Component\Csv\Reader;

class RowReader implements RowReaderInterface
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

    public function getRow(int $line): RowReaderInterface

    {
        return $this->getRows([$line]);
    }

    public function getRows(array $lines): RowReaderInterface
    {
        $items = [];
        foreach ($lines as $lineNr) {
            $this->cursor->seek($lineNr);
            $items[$lineNr] = $this->cursor->current();
        }

        $this->cursor->rewind();

        return new self(new ItemCollection($items));
    }

    public function getColumns(string...$columnNames): RowReaderInterface
    {
        $items = [];
        foreach ($this->getIterator() as $key => $row) {
            foreach ($columnNames as $columnName) {
                $items[$key][$columnName] = $row[$columnName];
            }
        }

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
        return new self($this->process($callable));
    }

    private function process(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            if (true === $callable($row)) {
                yield $key => $row;
            }
        }
    }

    public function getIterator(): \Iterator
    {
        return $this->cursor;
    }

    public function getValues(): array
    {
        return iterator_to_array($this->cursor);
    }
}
