<?php

namespace Misery\Component\Writer;

use Assert\Assert;
use Assert\Assertion;

class CsvWriter implements ItemWriterInterface
{
    public const DELIMITER = ';';

    private $delimiter;
    private $filename;
    private $handle;
    private $allowHeaders;

    private static $format = [
        'delimiter' => self::DELIMITER,
        'allow_headers' => true,
    ];

    public function __construct(
        string $filename,
        string $delimiter = self::DELIMITER,
        bool $allowHeaders = true
    ) {
        #Assertion::writeable($filename);

        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->allowHeaders = $allowHeaders;
        $this->handle = fopen($this->filename, 'wb+');

        Assertion::isResource($this->handle);
    }

    public static function createFromArray(array $setup): CsvWriter
    {
        Assert::that($setup)->keyIsset('filename');
        Assert::that($setup['filename'])->notEmpty();
        $format = array_merge(self::$format, $setup['format'] = []);
        Assert::that($format['allow_headers'])->boolean();
        Assert::that($format['delimiter'])->maxLength(1);

        return new self(
            $setup['filename'],
            $format['delimiter'],
            $format['allow_headers']
        );
    }

    public function write(array $data): void
    {
        $this->setHeader(array_keys($data));

        fputcsv($this->handle, array_values($data), $this->delimiter);
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