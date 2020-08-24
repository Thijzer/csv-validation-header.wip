<?php

namespace Misery\Component\Writer;

class CsvWriter
{
    public const DELIMITER = ';';

    /** @var string */
    private $delimiter;
    /** @var string */
    private $filename;
    /** @var resource */
    private $handle;
    /** @var bool */
    private $allowHeaders;

    public function __construct(
        string $filename,
        string $delimiter = self::DELIMITER,
        bool $allowHeaders = true
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->allowHeaders = $allowHeaders;
        $this->handle = fopen($this->filename, 'wb+');
    }

    public static function createFromArray(array $setup)
    {
        return new self(
            $setup['filename'],
            $setup['format']['delimiter']
        );
    }

    public function write(array $row): void
    {
        $this->setHeader(array_keys($row));

        fputcsv($this->handle, array_values($row), $this->delimiter);
    }

    public function close(): void
    {
        fclose($this->handle);
    }

    public function clear(): void
    {
        file_put_contents($this->filename, '');
    }

    public function __destruct()
    {
        $this->close();
    }

    public function setHeader(array $headers): void
    {
        if ($this->allowHeaders) {
            fputcsv($this->handle, $headers, $this->delimiter);
            $this->allowHeaders = false;
        }
    }
}