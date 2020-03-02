<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class StringToSerializeFormat implements StringFormat, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'unserialize';

    private $options = [
        'allowed_classes' => false,
    ];

    /** @inheritDoc */
    public function format(string $value)
    {
        return \unserialize($value, $this->options) ?? $value;
    }

    /**
     * @param array $value
     * @return string
     */
    public function reverseFormat($value)
    {
        return \serialize($value);
    }
}