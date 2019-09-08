<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cache\Local\InMemoryCache;
use Misery\Component\Common\Cache\SimpleCacheInterface;
use Misery\Component\Common\Cursor\CursorInterface;

class CsvReader implements ReaderInterface
{
    private $cursor;
    /** @var SimpleCacheInterface */
    private $cache;

    public function __construct(CursorInterface $cursor)
    {
        $this->cursor = $cursor;
        $this->cache = new InMemoryCache();
    }

    public function setCache(SimpleCacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function getCursor(): CursorInterface
    {
        return $this->cursor;
    }

    public function loop(callable $callable): void
    {
        foreach($this->cursor->getIterator() as $row) {
            $callable($row);
        }
    }

    public function getRow(int $line): array
    {
        return current($this->getRows([$line])) ?? [];
    }

    public function getColumn(string $columnName): array
    {
        if (false === $this->cache->has($columnName)) {
            $columnValues = [];
            $this->loop(function ($row) use (&$columnValues, $columnName) {
                $columnValues[$this->cursor->key()] = $row[$columnName];
            });

            $this->cache->set($columnName, $columnValues);

            return $columnValues;
        }

        return $this->cache->get($columnName) ?? [];
    }

    public function getColumns(array $columnNames): array
    {
        $columnValues = [];
        foreach ($columnNames as $columnName) {
            $columnValues[$columnName] = $this->getColumn($columnName);
        }

        return $columnValues;
    }

    public function indexColumnsReference(string ...$columnNames): array
    {
        $this->cache->set(
            $referenceKey = implode('|', $columnNames),
            $references = $this->combineReferences($this->getColumns($columnNames))
        );

        return [$referenceKey => $references];
    }

    private function combineReferences(array $arrays)
    {
        $concat = [];
        foreach ($arrays as $array) {
            foreach ($array as $pointer => $item) {
                $concat[$pointer] = isset($concat[$pointer]) ? $concat[$pointer].'|'.$item : $item;
            }
        }

        return $concat;
    }

    public function indexColumn(string $columnName): void
    {
        $this->getColumn($columnName);
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

    private function filter(callable $callable): array
    {
        $values = [];
        $this->loop(function ($row) use (&$values, $callable) {
            if (true === $callable($row)) {
                $values[$this->cursor->key()] = $row;
            }
        });

        return $values;
    }

    public function findOneBy(array $filter): array
    {
        return current($this->findBy($filter)) ?: [];
    }

    public function findBy(array $filter): array
    {
        $cursor = $this->cursor;
        foreach ($filter as $key => $value) {
            $this->cursor = new ItemCollection($this->processFilter($key, $value));
        }
        // rotate back the cursor
        $rows = $this->cursor;
        $this->cursor = $cursor;

        // fetch the values for these line numbers
        return $rows->getValues();
    }

    private function processFilter($key, $value): array
    {
        if ($this->cache->has($key)) {
            // cached lineNr to get rows per lineNr
            $rows = $this->getRows(array_keys($this->cache->filter($key, static function ($row) use ($value) {
                return $value === $row;
            })));
        } else {
            $rows = $this->filter(static function ($row) use ($value, $key) {
                return $row[$key] === $value;
            });
        }

        return $rows;
    }
}
