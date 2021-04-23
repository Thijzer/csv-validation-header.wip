<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class ReseatAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'reseat';

    /** @var array */
    private $options = [
        'from' => [],
        'to' => null,
    ];

    public function apply(array $item): array
    {
        $tmp = [];
        if ($this->options['from'] === 'all') {
            $tmp[$this->options['to']] = $item;

            return $tmp;
        }

        $keys = array_intersect($this->options['from'], array_keys($item));
        if (empty($keys)) {
            return $item;
        }

        foreach ($keys as $key) {
            $item[$this->options['to']][$key] = $item[$key];
            unset($item[$key]);
        }

        return $item;
    }
}