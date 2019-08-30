<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Processor\CsvDataProcessor;
use Misery\Component\Common\Processor\NullDataProcessor;
use Misery\Component\Csv\Cache\CacheCollector;

class CsvReader implements ReaderInterface
{
    public const FETCH_NO_REWIND_MODE = 'FETCH_NO_REWIND_MODE';

    private $cursor;
    private $processor;
    private $cache;

    private $options = [
        self::FETCH_NO_REWIND_MODE => false,
    ];

    public function __construct(CursorInterface $cursor)
    {
        $this->cursor = $cursor;
        $this->processor = new NullDataProcessor();
        $this->cache = new CacheCollector();
    }

    public function setProcessor(CsvDataProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function getCursor(): CursorInterface
    {
        return $this->cursor;
    }

    public function loop(callable $callable): void
    {
        while ($row = $this->cursor->current()) {
            $callable($this->processor->processRow($row));
            $this->cursor->next();
        }

        $this->cursor->rewind();
    }

    public function getRow(int $line): array
    {
        return $this->getRows([$line]);
    }

    public function getColumn(string $columnName): array
    {
        if (false === $this->cache->hasKey($columnName)) {
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

    public function getRows(array $lines): array
    {
        // flip to position the lines as keys
        $lines = array_flip($lines);

        $collect = [];
        while ($row = $this->cursor->current()) {
            if (isset($lines[$this->cursor->key()])) {
                $collect[$this->cursor->key()] = $this->processor->processRow($row);
                if (\count($collect) === \count($lines)) {
                    break;
                }
            }
            $this->cursor->next();
        }

        if (false === $this->options[self::FETCH_NO_REWIND_MODE]) {
            $this->cursor->rewind();
        }

        return $collect;
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

        if ($this->cache->hasKey($columnName)) {
            // fetch the correct line numbers
            $lines = array_keys($this->cache->filterCache($columnName, static function ($item) use ($filter) {
                return $filter[key($filter)] === $item;
            }));
        } else {
            $lines = array_keys($this->filter(static function ($item) use ($filter, $columnName) {
                return $item[$columnName] === $filter[$columnName];
            }));
        }

        // fetch the values for these line numbers
        return $this->getRows($lines);
    }

    public function clear(): void
    {
        $this->cursor->clear();
        $this->cache->clear();
    }
}
