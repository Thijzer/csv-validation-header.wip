<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;
use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class ArrayFlattenFormat implements Format, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'unflatten';

    private $options = [
        'separator' => '.',
    ];

    public function format($item): array
    {
        return ArrayFunctions::unflatten($item, $this->options['separator']);
    }

    public function reverseFormat($value): array
    {
        return ArrayFunctions::flatten($value, $this->options['separator']);
    }
}
