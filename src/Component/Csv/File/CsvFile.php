<?php

namespace Misery\Component\Csv\File;

class CsvFile
{
    public const DELIMITER = ';';
    public const ENCLOSURE = '"';
    public const ESCAPE = '\\';

    private $filename;
    private $references;
    private $delimiter;
    private $enclosure;
    private $escapeChar;

    public function __construct(
        string $filename,
        array $references,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE
    ) {
        $this->filename = $filename;
        $this->references = $references;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
    }
}