<?php

namespace Component\Csv\Reader;

class CsvReader implements ReaderInterface
{
    public function read(string $filename): CsvParserInterface
    {
        return CsvParser::create($filename);
    }
}