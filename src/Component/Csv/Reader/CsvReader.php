<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;

class CsvReader implements CsvReaderInterface
{
    private $cursor;

    public function __construct(CursorInterface $cursor)
    {
        $this->cursor = $cursor;
    }

    public function getIterator(): \Generator
    {
        return $this->cursor->getIterator();
    }

    public function loop(callable $callable): void
    {
        foreach($this->cursor->getIterator() as $row) {
            $callable($row);
        }
    }

    public function getRow(int $line): array
    {
        return current($this->getRows([$line])) ?: [];
    }

    public function getRows(array $lines): array
    {
        $collect = [];
        foreach ($lines as $lineNr) {
            $this->cursor->seek($lineNr);
            $collect[$lineNr] = $this->cursor->current();
        }

        $this->cursor->rewind();

        return $collect;
    }

    public function getColumn(string $columnName): array
    {
        $items = [];
        foreach ($this->getIterator() as $row) {
            $items[$this->cursor->key()] = $row[$columnName];
        }

        return $items;
    }

    public function getColumns(string...$columnNames): array
    {
        $columnValues = [];
        foreach ($columnNames as $columnName) {
            $columnValues[$columnName] = $this->getColumn($columnName);
        }

        return $columnValues;
    }
}
