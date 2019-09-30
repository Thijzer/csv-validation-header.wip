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

    public function getValues(): array
    {
        return iterator_to_array($this->cursor);
    }

    public function getRow(int $line): ItemCollection
    {
        return $this->getRows([$line]);
    }

    public function getRows(array $lines): ItemCollection
    {
        $items = [];
        foreach ($lines as $lineNr) {
            $this->cursor->seek($lineNr);
            $items[$lineNr] = $this->cursor->current();
        }

        $this->cursor->rewind();

        return new ItemCollection($items);
    }

    public function getColumn(string $columnName): ItemCollection
    {
        return $this->getColumns($columnName);
    }

    public function getColumns(string...$columnNames): ItemCollection
    {
        $items = [];
        foreach ($this->cursor->getIterator() as $key => $row) {
            foreach ($columnNames as $columnName) {
                $items[$columnName][$key] = $row[$columnName];
            }
        }

        return new ItemCollection($items);
    }
}
