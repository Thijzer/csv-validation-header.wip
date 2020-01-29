<?php

namespace Misery\Component\Common\Repository;

use Misery\Component\Common\Cache\Local\NameSpacedPoolCache;
use Misery\Component\Csv\Reader\RowReaderInterface;
use Misery\Component\Item\Builder\Combine\ReferencedValueBuilder;

/**
 * A doctrine compatible File Repository
 */
class FileRepository
{
    private $cache;
    private $reader;
    private $references;

    public function __construct(RowReaderInterface $reader,string ...$references)
    {
        $this->cache = new NameSpacedPoolCache();
        $this->reader = $reader;
        $this->indexColumnsReference(...$references);
        $this->references = $references;
    }

    public function find($id): array
    {
        $criteria = [];
        foreach ($this->references as $reference) {
            $criteria[$reference] = $id;
        }

        return $this->findOneBy($criteria);
    }

    public function findBy(array $criteria): array
    {
        return $this->reader->find($criteria)->getItems();
    }

    public function findOneBy(array $criteria): array
    {
        return current($this->findBy($criteria)) ?: [];
    }

    private function indexColumnsReference(string ...$columnNames): void
    {
        $this->cache->set(
            $uniqueReference = implode($sep = '|', $columnNames),
            $references = ReferencedValueBuilder::combine($this->reader, ...$columnNames)
        );
    }

    private function filter(callable $callable): array
    {
        return $this->reader->filter($callable)->getItems();
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