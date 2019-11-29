<?php

namespace Misery\Component\Common\Processor;

interface ProcessorAwareInterface
{
    public function setProcessor(CsvDataProcessorInterface $processor): void;
}