<?php

namespace Misery\Component\Source;

use Misery\Component\Reader\ItemReaderInterface;
use Misery\Component\Source\Command\ExecuteSourceCommandInterface;

class SourceFilter
{
    private $command;

    public function __construct(ExecuteSourceCommandInterface $command)
    {
        $this->command = $command;
    }

    /**
     * @param array $item
     * @return ItemReaderInterface|array
     */
    public function filter(array $item)
    {
        $options = $this->createOptionsFromItem($item);

        // prep the options
        return $this->command->executeWithOptions($options);
    }

    private function createOptionsFromItem(array $item): array
    {
        $options = $this->command->getOptions();

        foreach ($options['stmt'] as $key => $value) {
            if (strpos($value, '$') !== false && isset($item[$key])) {
                $options['stmt'][$key] = $item[$key];
            }
        }

        return $options;
    }
}