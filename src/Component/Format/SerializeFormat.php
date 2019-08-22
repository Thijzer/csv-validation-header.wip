<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class SerializeFormat implements Format, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'unserialize';

    private $options = [
        'allowed_classes' => false,
    ];

    public function format($value)
    {
        return \unserialize($value, $this->options) ?? $value;
    }

    public function reverseFormat($value)
    {
        return \serialize($value);
    }
}