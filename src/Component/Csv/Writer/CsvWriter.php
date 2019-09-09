<?php

namespace Misery\Component\Csv\Writer;

class CsvWriter
{
    public const DELIMITER = ';';
    public const UTF8_BOM = "\xEF\xBB\xBF";
    public const LINE_ENDING = PHP_EOL;

    private $delimiter;
    private $filename;
    private $bom;
    private $handle;
    private $allowHeaders = true;
    private $headers;

    public function __construct(
        string $filename,
        string $delimiter = self::DELIMITER,
        string $bom = null
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->bom = $bom;
    }

    public function disableHeaders(): self
    {
        $this->allowHeaders = false;

        return $this;
    }

    private function getHandle()
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'wb+');
            if ($this->bom) {
                fwrite($this->handle, $this->bom);
            }
        }

        return $this->handle;
    }

    public function write(array $row): void
    {
        $handle = $this->getHandle();

        $this->setHeader(array_keys($row));

        fputcsv($handle, array_values($row), $this->delimiter);
    }

    public function close(): void
    {
        fclose($this->getHandle());
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
        if (null === $this->headers && $this->allowHeaders) {
            $this->headers = $headers;
            $this->write($headers);
        }
    }
}