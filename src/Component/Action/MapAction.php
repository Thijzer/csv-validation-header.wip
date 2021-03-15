<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class MapAction implements ActionInterface, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'map_from_list';

    /** @var array */
    private $options = [
        'key' => null,
        'list' => [],
    ];

    public function apply(array $item): array
    {
        $value = $item[$this->options['key']] ?? null;
        if ($value && array_key_exists($value, $this->options['list'])) {
            $item[$this->options['key']] = $this->options['list'][$value];
        }

        return $item;
    }
}