<?php

namespace Misery\Component\Common\Repository;

use Misery\Component\Common\Cache\Local\NameSpacedPoolCache;
use Misery\Component\Csv\Reader\CsvReaderInterface;

class FileRepository
{
    private $cache;
    private $reader;

    public function __construct(CsvReaderInterface $reader, ...$references)
    {
        $this->cache = new NameSpacedPoolCache();
        $this->reader = $reader;
        $this->indexColumnsReference($references);
    }

    public function find($id)
    {
    }

    public function findBy(array $filter): array
    {
        //$cursor = $this->cursor;
        foreach ($filter as $key => $value) {
            //$this->cursor = new ItemCollection($this->processFilter($key, $value));
        }
        // rotate back the cursor
        //$rows = $this->cursor;
        //$this->cursor = $cursor;

        // fetch the values for these line numbers
        //return $rows->getValues();
    }

    public function findOneBy(array $filter): array
    {
        return current($this->findBy($filter)) ?: [];
    }

    private function indexColumnsReference(string ...$columnNames): array
    {
        $this->cache->set(
            $referenceKey = implode('|', $columnNames),
            $references = $this->combineReferences($this->reader->getColumns($columnNames))
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

    private function filter(callable $callable): array
    {
        $values = [];
        foreach ($this->reader->getIterator() as $key => $row) {
            if (true === $callable($row)) {
                $values[$key] = $row;
            }
        }

        return $values;
    }

    private function processFilter($key, $value): array
    {
        if ($this->cache->has($key)) {
            // cached lineNr to get rows per lineNr
            $rows = $this->reader->getRows([$this->cache->get($key, $value)]);
        } else {
            $rows = $this->filter(static function ($row) use ($value, $key) {
                return $row[$key] === $value;
            });
        }

        return $rows;
    }

    public function __destruct()
    {
        $this->cache->clear();
    }
}