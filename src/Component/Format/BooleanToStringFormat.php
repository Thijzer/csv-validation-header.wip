<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class BooleanToStringFormat implements StringFormat, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'boolean';

    /** @var array */
    private $options = [
        'true'  => '1',
        'false' => '0',
    ];

    /** @inheritDoc */
    public function format(string $value):? bool
    {
        if ($value === $this->options['true']) {
            return true;
        }

        if ($value === $this->options['false']) {
            return false;
        }

        return null;
    }

    /**
     * @param bool $value
     * @return string
     */
    public function reverseFormat($value): string
    {
        if ($value === true) {
            return $this->options['true'];
        }

        if ($value === false) {
            return $this->options['false'];
        }
    }
}