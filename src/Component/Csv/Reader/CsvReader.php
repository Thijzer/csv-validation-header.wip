<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Processor\CsvDataProcessor;
use Misery\Component\Csv\Cache\CacheCollector;

class CsvReader implements ReaderInterface
{
    private $cursor;
    private $processor;
    private $cache;

    public function __construct(CursorInterface $cursor, CsvDataProcessor $processor = null)
    {
        $this->cursor = $cursor;
        $this->processor = $processor;
        $this->cache = new CacheCollector();
    }

    public function getCursor(): CursorInterface
    {
        return $this->cursor;
    }

    public function loop(callable $callable): void
    {
        if ($this->processor) {
            while ($this->cursor->valid()) {
                $callable($this->processor->processRow($this->cursor->current()));
                $this->cursor->next();
            }
        } else {
            while ($this->cursor->valid()) {
                $callable($this->cursor->current());
                $this->cursor->next();
            }
        }

        $this->cursor->rewind();
    }

    public function getRow(int $line): array
    {
        $data = $this->getRows([$line]);

        //$this->cursor = new ItemCollection($data);

        return $data;
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
       // if ($this->cache->hasCaches($lines)) {
       //     return $this->cache->getCaches($lines);
       // }

        $collect = [];
        $this->loop(function ($row) use (&$collect, $lines) {
            if (\in_array($this->cursor->key(), $lines, true)) {
                $collect[$this->cursor->key()] = $row;
            }
        });

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

        if ($this->cache->hasCache($columnName)) {

            // fetch the correct line numbers
            $lines = array_keys($this->cache->filterCache($columnName, static function ($item) use ($filter) {
                return $filter[key($filter)] === $item;
            }));

            //if ($mode === self::EAGER_MODE && !$this->cache->hasCaches($lines)) {
                // fill the cache
                //$this->cursor = new ItemCollection($this->getRows(range($lines[0],$lines[0]+self::MED_CACHE_SIZE)));
               // $this->cache->setCaches($this->getRows(range($lines[0],$lines[0]+self::MED_CACHE_SIZE)));
            //}

            // fetch the values for these line numbers
            $data = $this->getRows($lines);

            //$this->cursor = new ItemCollection($data);

            return $data;
        }

        $data = $this->filter(static function ($item) use ($filter, $columnName) {
            return $item[$columnName] === $filter[$columnName];
        });

        //$this->cursor = new ItemCollection($data);

        return $data;
    }

    public function clear(): void
    {
        $this->cursor->clear();
        $this->cache->clear();
    }
}