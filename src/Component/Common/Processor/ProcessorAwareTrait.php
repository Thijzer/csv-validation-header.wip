<?php

namespace Misery\Component\Common\Processor;

trait ProcessorAwareTrait
{
    private $processor;

    public function setProcessor(CsvDataProcessorInterface $processor): void
    {
        $this->processor = $processor;
    }
}