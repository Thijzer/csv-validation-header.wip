<?php

namespace RFC\Component\Csv\Reader;

interface ReaderInterface
{
    public function read(string $filename);
}