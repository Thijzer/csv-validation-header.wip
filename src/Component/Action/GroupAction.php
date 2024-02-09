<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;

class GroupAction implements OptionsInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    public const NAME = 'group';

    /** @var array */
    private $options = [
        'name' => null,
        'actionProcessor' => null,
    ];

    public function apply(array $item): array
    {
        if ($this->getOption('name') && !$this->getOption('actionProcessor')) {
            $this->setOption(
                'actionProcessor',
                $this->getConfiguration()->getGroupedActions($this->getOption('name'))
            );
        }

        if ($this->getOption('actionProcessor')) {
            $item = $this->getOption('actionProcessor')->process($item);
        }

        return $item;
    }
}