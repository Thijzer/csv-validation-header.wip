<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;

class EmptyAction implements OptionsInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    public const NAME = 'empty';

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
        if ($field && !is_array($item[$field])) {
            return $item;
        }

        foreach($list as $key) {
            if (isset($item[$field][$key]) && is_array($item[$field][$key])) {
                foreach ($item[$field][$key] as $index => $value) {
                    if (is_array($value) && isset($value['data'])) {
                        $item[$field][$key][$index]['data'] = null;
                    }
                }
            }
        }

        return $item;
    }
}