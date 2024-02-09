<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;

class SkipAction implements OptionsInterface, ActionInterface
{
    use OptionsTrait;

    public const NAME = 'skip';

    /** @var array */
    private $options = [
        'field' => null,
        'state' => 'EMPTY',
        'skip_message' => ''
    ];

    public function apply(array $item): array
    {
        $field = $this->getOption('field');
        $state = $this->getOption('state');
        $message = $this->getOption('skip_message', '');

        $message = $this->getOption('skip_message', '');
        $forceSkip = $this->getOption('force_skip', false);

        if ($forceSkip) {
            throw new SkipPipeLineException($message);
        }

        if (is_array($field) && isset($field['code']) && isset($field['index'])) {
            $value = (isset($item[$field['code']][$field['index']])) ? $item[$field['code']][$field['index']] : null;
        } else {
            $value = $item[$field] ?? null;
        }

        if (empty($value) && $state === 'EMPTY') {
            throw new SkipPipeLineException($message);
        }

        if ($value === $state) {
            throw new SkipPipeLineException($message);
        }

        return $item;
    }
}