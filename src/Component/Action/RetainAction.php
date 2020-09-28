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
        $keys = array_intersect($this->options['keys'], array_keys($item));
        if (empty($keys)) {
            return $item;
        }

        $tmp = [];
        foreach ($keys as $key) {
            $tmp[$key] = $item[$key];
        }

        return $tmp;
    }
}