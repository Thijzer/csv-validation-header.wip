<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;

class UnsetAction implements OptionsInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    public const NAME = 'unset';

    /** @var array */
    private $options = [
        'field' => null,
        'list' => null,
    ];

    public function apply(array $item): array
    {
        $field = $this->getOption('field');
        $list = $this->getOption('list');

        // validation
        if (!is_array($item[$field])) {
            return $item;
        }

        foreach($list as $key) {
            unset($item[$field][$key]);
        }

        return $item;
    }
}