<?php

namespace Misery\Component\Source;

use Misery\Component\Reader\ItemReaderInterface;
use Misery\Component\Source\Command\ExecuteSourceCommandInterface;

class SourceFilter
{
    private $filter;
    private $command;

    public function __construct(ExecuteSourceCommandInterface $command, array $filter)
    {
        $this->filter = $filter;
        $this->command = $command;
    }

    public function filter(array $options): ItemReaderInterface
    {
        // we miss the tech to map correctly here
        $options = array_merge($this->filter, $options);
        // prep the options
        return $this->command->executeWithOptions(['filter' => $options]);
    }
}