<?php

namespace Misery\Component\Csv\Reader;

interface CsvCursorInterface extends \Countable, \SeekableIterator
{
    public function getHeaders(): array;
    public function hasHeaders(): bool;
}