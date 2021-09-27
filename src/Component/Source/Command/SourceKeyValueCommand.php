<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Item\Builder\KeyValuePairBuilder;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;

class SourceKeyValueCommand implements SourceAwareInterface, ExecuteSourceCommandInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $options = [
        'list' => null,
        'key' => null,
        'value' => null,
    ];

    public function execute(): array
    {
        return KeyValuePairBuilder::build(
            $this->getSource()->getCachedReader(),
            $this->getOption('key'),
            $this->getOption('value')
        );
    }

    public function executeWithOptions(array $options)
    {
        $this->setOptions($options);

        return $this->execute();
    }

    public function getName(): string
    {
        return 'key_value_pair';
    }
}