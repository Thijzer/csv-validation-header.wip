<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class DateTimeFormat implements Format, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'datetime';

    private $options = [
        'format' => 'd/m/Y H:i:s'
    ];

    public function format($value): \DateTime
    {
        return \DateTime::createFromFormat($this->options['format'], $value);
    }


    public function reverseFormat($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format($this->options['format']);
        }

        return null;
    }
}