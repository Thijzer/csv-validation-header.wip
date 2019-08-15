<?php

namespace Component\Csv\Reader;

use Component\Csv\Cache\CacheCollector;
use Component\Csv\Exception\InvalidCsvElementSizeException;

class CsvParser implements CsvParserInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $headers;
    private $file;
    private $count;
    private $cache;

    public function __construct(
        \SplFileObject $file,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ) {
        $this->file = $file;
        $this->cache = new CacheCollector();
        ini_set('auto_detect_line_endings', true);

        $file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $file->setCsvControl($delimiter, $enclosure, $escapeChar);

        // set headers
        $this->headers = $file->current();
        $file->next();
    }

    public static function create(
        string $filename,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ): self {
        return new self(new \SplFileObject($filename), $delimiter, $enclosure, $escapeChar);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeaders(): bool
    {
        return null !== $this->headers;
    }

    public function loop(callable $callable): void
    {
        while ($row = $this->current()) {
            $callable($row);
            $this->next();
        }
        $this->rewind();
    }

    public function getRow(int $line): array
    {
        $columnValues = [];
        $this->loop(function ($row) use (&$columnValues, $line) {
            if ($this->file->key() === $line) {
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
                $columnValues[$this->file->key()] = $row[$columnName];
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

    public function findOneBy(array $filter): array
    {
        return current($this->findBy($filter));
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

    private function getRows(array $lines): array
    {
        return array_map(function ($line) {
            return $this->getRow($line);
        }, $lines);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $current = $this->file->current();
        if (!$current || !$this->hasHeaders()) {
            return $current;
        }

        // here we need to use the filter
        $row = @array_combine($this->headers, $current);
        if (false === $row) {
            throw new InvalidCsvElementSizeException($this->file->getFilename(), $this->key());
        }

        return $row;
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        $this->file->next();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return $this->file->valid();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->file->rewind();
        // allow headers
        $this->hasHeaders()? $this->next(): null;
    }

    /**
     * {@inheritDoc}
     */
    public function seek($pointer): void
    {
        $this->file->seek($pointer);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        if (null === $this->count) {
            $position = $this->key();
            $this->count = iterator_count($this);
            $this->seek($position);
        }

        return $this->count;
    }
}