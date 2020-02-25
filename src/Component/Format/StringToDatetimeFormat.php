<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class StringToDatetimeFormat implements StringFormat, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'datetime';

    /** @var array */
    private $options = [
        'format' => 'd/m/Y H:i:s'
    ];

    /** @inheritDoc */
    public function format(string $value): \DateTime
    {
        return \DateTime::createFromFormat($this->options['format'], $value);
    }

    /**
     * @param \DateTime $value
     * @return string
     */
    public function reverseFormat($value): string
    {
        return $value->format($this->options['format']);
    }
}