<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Csv\Exception\InvalidCsvElementSizeException;

class CsvParser implements CsvCursorInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $headers;
    private $file;
    private $count;

    public function __construct(
        \SplFileObject $file,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ) {
        $this->file = $file;
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