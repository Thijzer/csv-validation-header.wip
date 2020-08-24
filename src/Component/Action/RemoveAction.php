<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class RemoveAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'remove';

    /** @var array */
    private $options = [
        'keys' => [],
    ];

    public function apply(array $item): array
    {
        foreach ($this->options['keys'] as $key) {
            unset($item[$key]);
        }

        return $item;
    }
}