<?php

namespace Misery\Component\Parser;

use Assert\Assertion;
use Misery\Component\Common\Cursor\CursorInterface;

class CsvParser implements CursorInterface
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    /** @var array|false|mixed|string */
    private $headers;
    /** @var \SplFileObject */
    private $file;
    /** @var int|null */
    private $count;

    public function __construct(
        \SplFileObject $file,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ) {
        Assertion::file($file->getRealPath());

        $this->file = $file;
        ini_set('auto_detect_line_endings', '1');

        $file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $file->setCsvControl($delimiter, $enclosure, $escapeChar);

        if (null === $this->headers) {
            $this->headers = $this->current();
            $this->next();
        }
    }

    public static function create(
        string $filename,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ): self {
        return new self(new \SplFileObject($filename), $delimiter, $enclosure, $escapeChar);
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
            yield $this->key() => $this->current();
            $this->next();
        }

        $this->rewind();
    }

    /**
     * {@inheritDoc}
     * @throws Exception\InvalidCsvElementSizeException
     * @return false|array
     */
    public function current()
    {
        $current = $this->file->current();
        if (false === $current || null === $this->headers) {
            return $current;
        }

        // here we need to use the filter
        $row = @array_combine($this->headers, $current);
        if (!is_array($row)) {
            throw new Exception\InvalidCsvElementSizeException(
                $this->file->getFilename(),
                $this->key(),
                $current,
                $this->headers
            );
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
        if (false === $this->valid()) {
            $this->count = $this->key() - 1;
        }

        $this->count();
        $this->file->rewind();

        // move 1 up for the headers
        $this->next();
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
            $this->loop(static function (){});
        }

        return $this->count;
    }
}