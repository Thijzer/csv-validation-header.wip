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

        foreach ($options['filter'] as $key => $value) {
            $field = ltrim($value, '$');
            if (strpos($value, '$') !== false && isset($item[$field])) {
                $options['criteria'][$key] = $item[$field];
                unset($options['filter']);
            }
        }

        return $options;
    }
}