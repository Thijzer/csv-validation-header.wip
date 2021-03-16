<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;

class SourceFilterCommand implements ExecuteSourceCommandInterface, SourceAwareInterface, OptionsInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $options = [];

    public function execute()
    {
        return new ItemCollection(
            $this->getSource()->getReader()->find($this->getOptions())->getItems()
        );
    }

    public function getName(): string
    {
        return 'filter';
    }
}