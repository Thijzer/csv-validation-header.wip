<?php

namespace Component\Csv\Reader;

class CsvReader implements ReaderInterface
{
    public function read(string $filename): CsvParser
    {
        return CsvParser::create($filename);
    }
}