<?php

namespace Misery\Component\Format;

use Misery\Component\Common\Format\Format;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class BooleanFormat implements Format, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'boolean';

    private $options = [
        'true'  => '1',
        'false' => '0',
    ];

    public function format($value):? bool
    {
        if ($value === $this->options['true']) {
            return true;
        }

        if ($value === $this->options['false']) {
            return false;
        }

        return null;
    }

    public function reverseFormat($value)
    {
        if ($value === true) {
            return $this->options['true'];
        }

        if ($value === false) {
            return $this->options['false'];
        }

        return null;
    }
}