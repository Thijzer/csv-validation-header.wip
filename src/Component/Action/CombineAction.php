<?php

namespace Misery\Component\Action;

use Misery\Component\Akeneo\AkeneoValuePicker;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class CombineAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'combine';

    /** @var array */
    private $options = [
        'keys' => [],
        'locale' => null,
        'scope' => null,
        'separator' => ' ',
        'header' => null,
    ];

    public function apply(array $item): array
    {
        $values = [];
        foreach ($this->options['keys'] as $key) {
            $values[] = AkeneoValuePicker::pick($item, $key, $this->options);
        }

        $item[$this->options['header']] = implode($this->options['separator'], $values);

        return $item;
    }
}
