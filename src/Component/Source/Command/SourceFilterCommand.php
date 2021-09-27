<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;

class SourceFilterCommand implements ExecuteSourceCommandInterface, SourceAwareInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $options = [
        'return' => [],
        'filter' => [],
    ];

    public function execute()
    {
        $items = $this->getSource()->getCachedReader()->find($this->getOption('stmt'));

        if (!empty($this->getOption('return'))) {
            return ReferenceBuilder::buildValues(
                $items,
                $this->getOption('return')
            );
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