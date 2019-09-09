<?php

namespace Misery\Component\Csv\Reader;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Processor\CsvDataProcessorInterface;
use Misery\Component\Common\Processor\NullDataProcessor;
use Misery\Component\Csv\Exception\InvalidCsvElementSizeException;

class CsvParser implements CsvInterface, CursorInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $headers;
    private $file;
    private $count;
    private $processor;

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
        $this->processor = new NullDataProcessor();

        $this->setHeaders();
    }

    public static function create(
        string $filename,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ): self {
        return new self(new \SplFileObject($filename), $delimiter, $enclosure, $escapeChar);
    }

    public function setProcessor(CsvDataProcessorInterface $processor): void
    {
        $this->processor = $processor;
    }

    public function setHeaders(): void
    {
        if ($row = $this->current()) {
            $this->headers = $row;
            $this->next();
        }
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeaders(): bool
    {
        return null !== $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function loop(callable $callable): void
    {
        foreach ($this->getIterator() as $row) {
            $callable($row);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Generator
    {
        while ($this->valid()) {
            yield $this->current();
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
        if (null === $row) {
            throw new InvalidCsvElementSizeException($this->file->getFilename(), $this->key());
        }

        return $this->processor->processRow($row);
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
        if (false === $this->valid()) {
            $this->count = $this->key() - (int) $this->hasHeaders();
        }

        $this->count();
        $this->file->rewind();

        false == $this->hasHeaders() ? $this->setHeaders(): $this->next();
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
            $this->loop(function (){});
        }

        return $this->count;
    }
}