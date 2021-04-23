<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;

class SourceFilterCommand implements ExecuteSourceCommandInterface, SourceAwareInterface, OptionsInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $options = [
        'return' => [],
        'filter' => [],
    ];

    public function execute()
    {
        $items = $this->getSource()->getReader()->find($this->getOption('filter'));

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
        $this->setOptions($options);

        return $this->execute();
    }

    public function getName(): string
    {
        return 'filter';
    }
}