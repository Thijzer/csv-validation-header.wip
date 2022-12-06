<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class RetainAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'retain';

    /** @var array */
    private $options = [
        'keys' => [],
    ];

    public function apply(array $item): array
    {
        $options = $this->options['keys'];
        $arrayValuesToKeep = [];
        foreach ($options as $key => $value) {
            if (strpos($value, '-') === false) {
                continue;
            }

            $value = explode('-', $value);
            if (!isset($arrayValuesToKeep[$value[0]])) {
                $arrayValuesToKeep[$value[0]] = [];
            }

            $arrayValuesToKeep[$value[0]][] = $value[1];
            $options[] = $value[0];
        }

        $keys = array_intersect($options, array_keys($item));
        if (empty($keys)) {
            return $item;
        }

        $tmp = [];
        foreach ($keys as $key) {
            if (isset($arrayValuesToKeep[$key])) {
                foreach ($arrayValuesToKeep[$key] as $value) {
                    if (!isset($item[$key][$value])) {
                        continue;
                    }

                    $tmp[$key . '-' . $value] = $item[$key][$value];
                }

                continue;
            }


            $tmp[$key] = $item[$key];
        }

        return $tmp;
    }
}