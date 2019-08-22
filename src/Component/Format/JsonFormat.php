<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class JsonFormat implements Format, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'json_decode';

    private $options = [
        'associative' => true,
        'depth' => 512,
        'options' => [],
    ];

    public function format($value)
    {
        return \json_decode(
            $value,
            $this->options['associative'],
            $this->options['depth'],
            $this->options['options']
        ) ?? $value;
    }

    public function reverseFormat($value)
    {
        return \json_encode(
            $value,
            $this->options['options'],
            $this->options['depth']
        );
    }
}