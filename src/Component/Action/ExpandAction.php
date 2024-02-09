<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class ExpandAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'expand';

    /** @var array */
    private $options = [
        'set' => null,
        'list' => null,
    ];

    public function apply(array $item): array
    {
        return array_replace_recursive($this->getOption('set', $this->getOption('list', [])), $item);
    }
}