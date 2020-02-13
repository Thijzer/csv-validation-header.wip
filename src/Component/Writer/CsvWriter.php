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
    private $allowHeaders = true;

    public function __construct(
        string $filename,
        string $delimiter = self::DELIMITER,
        bool $allowHeaders = false
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->allowHeaders = $allowHeaders;
        $this->handle = fopen($this->filename, 'wb+');
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
            $this->write($headers);
        }
    }
}