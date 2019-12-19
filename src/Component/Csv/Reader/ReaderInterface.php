<?php

namespace Misery\Component\Csv\Reader;

interface ReaderInterface
{
    public function read(): \Iterator;
}