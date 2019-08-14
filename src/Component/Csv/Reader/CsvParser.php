<?php

namespace RFC\Component\Csv\Reader;

use RFC\Component\Csv\Cache\CacheCollector;

class CsvParser implements \Countable, \SeekableIterator
{
    public const DEFAULT_DELIMITER = ',';
    public const DEFAULT_ENCLOSURE = '"';
    public const DEFAULT_ESCAPE = '\\';

    private $headers;
    private $file;
    private $count;
    private $delimiter;
    private $cache;

    public function __construct(
        \SplFileObject $file,
        $delimiter = self::DEFAULT_DELIMITER,
        $enclosure = self::DEFAULT_ENCLOSURE,
        $escapeChar = self::DEFAULT_ESCAPE
    ) {
        ini_set('auto_detect_line_endings', true);
        $file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );

        $file->setCsvControl($delimiter, $enclosure, $escapeChar);

        $this->delimiter = $delimiter;
        $this->headers = (array) $file->current();

        $this->cache = new CacheCollector();

        // allow headers
        $file->next();
        $this->file = $file;
    }

    public static function create(
        string $filename,
        $delimiter = self::DEFAULT_DELIMITER,
        $enclosure = self::DEFAULT_ENCLOSURE,
        $escapeChar = self::DEFAULT_ESCAPE
    ): self {
        return new self(new \SplFileObject($filename), $delimiter, $enclosure, $escapeChar);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeaders(): bool
    {
        return !empty($this->headers);
    }

    public function loop(callable $callable): void
    {
        while ($row = $this->current()) {
            $callable($row);
            $this->next();
        }
        $this->rewind();
    }

    /* @todo the default method is much slower then the hacky file() method */
    public function getRow(int $line)
    {
        $line = @file($this->file->getRealPath())[$line] ?? false;
        $line = str_replace(array("\n", "\r"), '', $line);
        $current = explode($this->delimiter, $line);
        $row = @array_combine($this->headers, $current);

        return $row;
    }

    /**
     * Returns the Column values of any given columnName
     *
     * @param string $columnName
     * @return array
     */
    public function getColumn(string $columnName): array
    {
        if (!$this->cache->hasCache($columnName)) {
            $columnValues = [];
            $this->loop(function ($row) use (&$columnValues, $columnName) {
                $columnValues[$this->file->key()] = $row[$columnName];
            });

            $this->cache->setCache($columnName, $columnValues);

            return $columnValues;
        }

        $this->cache->getCache($columnName);
    }

    public function addSearchableColumns(array $columnNames): void
    {
        foreach ($columnNames as $columnName) {
            $this->addSearchableColumn($columnName);
        }
    }

    public function addSearchableColumn(string $columnName): void
    {
        $this->cache->setCache($columnName, $this->getColumn($columnName));
    }

    public function findOneBy(array $filter)
    {
        return current($this->findBy($filter));
    }

    public function findBy(array $filter)
    {
        $columnName = key($filter);

        if ($this->cache->hasCache($columnName)) {

            // fetch the correct line numbers
            $lines = $this->cache->filterCache($columnName, function ($item) use ($filter) {
                return $filter[key($filter)] === $item;
            });

            // fetch the values for these line numbers
            return array_values(array_map(function ($line) {
                $a = $this->get($line);
                return $a;
            }, $lines));
        }

        return $this->filter(function ($item) use ($filter, $columnName) {
            return $item[$columnName] === $filter[$columnName];
        });
    }

    public function filter(callable $callable): array
    {
        $values = [];
        $this->loop(function ($row) use (&$values, $callable) {
            if (true === $callable($row)) {
                $values[] = $row;
            }
        });

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): array
    {
        $current = $this->file->current();
        if (!$current || !$this->hasHeaders()) {
            return $current;
        }

        $row = @array_combine($this->headers, $current);
        if (false === $row) {
            throw new InvalidCsvElementSizeException($this->file->getFilename(), $this->key());
        }

        return array_map(function ($val) {
            return '' === $val ? null : $val;
        }, $row);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->file->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->file->rewind();
        // allow headers
        $this->next();
    }

    /**
     * {@inheritdoc}
     */
    public function seek($pointer)
    {
        $this->file->seek($pointer);
    }

    public function count(): int
    {
        if (null === $this->count) {
            $position = $this->key();
            $this->count = iterator_count($this) - (int) $this->hasHeaders();
            $this->seek($position);
        }

        return $this->count;
    }
}