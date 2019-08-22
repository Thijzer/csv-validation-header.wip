<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Processor\CsvDataProcessor;
use Misery\Component\Csv\Cache\CacheCollector;

class CsvReader implements ReaderInterface
{
    private $cursor;
    private $processor;
    private $cache;

    public function __construct(CsvCursorInterface $cursor, CsvDataProcessor $processor = null)
    {
        $this->cursor = $cursor;
        $this->processor = $processor;
        $this->cache = new CacheCollector();
    }

    public function getCursor(): CsvCursorInterface
    {
        return $this->cursor;
    }

    public function loop(callable $callable): void
    {
        while ($row = $this->cursor->current()) {
            $callable($this->processor ? $this->processor->processRow($row): $row);
            $this->cursor->next();
        }
        $this->cursor->rewind();
    }

    public function getRow(int $line): array
    {
        $columnValues = [];
        $this->loop(function ($row) use (&$columnValues, $line) {
            if ($this->cursor->key() === $line) {
                $columnValues = $row;
            }
        });

        return $columnValues;
    }

    public function getColumn(string $columnName): array
    {
        if (false === $this->cache->hasCache($columnName)) {
            $columnValues = [];
            $this->loop(function ($row) use (&$columnValues, $columnName) {
                $columnValues[$this->cursor->key()] = $row[$columnName];
            });

            $this->cache->setCache($columnName, $columnValues);

            return $columnValues;
        }

        return $this->cache->getCache($columnName) ?? [];
    }

    public function indexColumns(string ...$columnNames): void
    {
        foreach ($columnNames as $columnName) {
            $this->indexColumn($columnName);
        }
    }

    public function indexColumn(string $columnName): void
    {
        $this->cache->setCache($columnName, $this->getColumn($columnName));
    }

    private function getRows(array $lines): array
    {
        return array_map(function ($line) {
            return $this->getRow($line);
        }, $lines);
    }

    private function filter(callable $callable): array
    {
        $values = [];
        $this->loop(static function ($row) use (&$values, $callable) {
            if (true === $callable($row)) {
                $values[] = $row;
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
        $columnName = key($filter);

        if ($this->cache->hasCache($columnName)) {

            // fetch the correct line numbers
            $lines = $this->cache->filterCache($columnName, static function ($item) use ($filter) {
                return $filter[key($filter)] === $item;
            });

            // fetch the values for these line numbers
            return array_values($this->getRows($lines));
        }

        return $this->filter(static function ($item) use ($filter, $columnName) {
            return $item[$columnName] === $filter[$columnName];
        });
    }
}