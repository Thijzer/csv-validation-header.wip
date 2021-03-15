<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Reader\ReaderInterface;

class PipeReader implements PipeReaderInterface
{
    /** @var ReaderInterface */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function read()
    {
        return $this->reader->read();
    }
}