<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class SetValueAction implements ActionInterface, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'set';

    /** @var array */
    private $options = [
        'key' => null,
        'value' => null,
    ];

    public function apply(array $item): array
    {
        $item[$this->options['key']] = $this->options['value'];

        return $item;
    }
}