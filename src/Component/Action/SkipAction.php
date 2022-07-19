<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;

class SkipAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'skip';

    /** @var array */
    private $options = [
        'field' => null,
        'state' => 'EMPTY',
    ];

    public function apply(array $item): array
    {
        $field = $this->getOption('field');
        $state = $this->getOption('state');

        $value = $item[$field] ?? null;

        if (empty($value) && $state === 'EMPTY') {
            throw new SkipPipeLineException();
        }

        if ($value === $state) {
            throw new SkipPipeLineException();
        }

        return $item;
    }
}