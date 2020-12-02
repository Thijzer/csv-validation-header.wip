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
        $keys = array_intersect($this->options['keys'], array_keys($item));
        if (empty($keys)) {
            return $item;
        }

        $tmp = [];
        foreach ($keys as $key) {
            $tmp[$this->options['to']][$key] = $item[$key];
        }

        return $tmp;
    }
}