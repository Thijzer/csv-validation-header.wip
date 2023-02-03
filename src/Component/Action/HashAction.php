<?php

namespace Misery\Component\Action;

use Misery\Component\Akeneo\AkeneoValuePicker;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class HashAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'hash';

    private $options = [
        'key' => null
    ];

    public function apply(array $item): array
    {
        if (array_key_exists($this->options['key'], $item)) {
            $item[$this->getOption('key')] = (string) crc32($item[$this->getOption('key')]);
        }

        return $item;
    }
}
