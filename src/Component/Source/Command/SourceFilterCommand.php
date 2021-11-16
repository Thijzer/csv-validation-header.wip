<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;

class SourceFilterCommand implements ExecuteSourceCommandInterface, SourceAwareInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $reader;

    private $options = [
        'return' => [],
        'cache' => [],
        'filter' => null,
    ];

    public function execute()
    {
        if ($this->reader === null) {
            $this->reader = $this->getSource()->getCachedReader($this->getOption('cache'));
        }

        $items = $this->reader->find($this->getOption('filter'));

        if (!empty($this->getOption('return'))) {
            return array_map(function (array $item) {
                return $item[$this->getOption('return')];
            }, $items->getItems());
        }

        return $items;
    }

    public function executeWithOptions(array $options)
    {
        $staleOptions = $this->getOptions();

        $this->setOptions($options);

        $result = $this->execute();

        $this->setOptions($staleOptions);

        return $result;
    }

    public function getName(): string
    {
        return 'filter';
    }
}