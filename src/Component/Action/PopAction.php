<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class PopAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'pop';

    /** @var array */
    private $options = [
        'field' => null,
        'separator' => null,
    ];

    public function apply(array $item): array
    {
        $value = $item[$this->options['field']] ?? null;
        if (null === $value) {
            return $item;
        }

        $value = explode($this->options['separator'], $value);
        $item[$this->options['field']] = array_pop($value);

        return $item;
    }
}