<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class StringToJsonFormat implements StringFormat, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'json_decode';

    /** @var array */
    private $options = [
        'associative' => true,
        'depth' => 512,
        'options' => [],
    ];

    /** @inheritDoc */
    public function format(string $value)
    {
        return \json_decode(
            $value,
            $this->options['associative'],
            $this->options['depth'],
            $this->options['options']
        ) ?? $value;
    }

    /**
     * @param array $value
     * @return false|string
     */
    public function reverseFormat($value)
    {
        return \json_encode(
            $value,
            $this->options['options'],
            $this->options['depth']
        );
    }
}