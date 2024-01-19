<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class FrameAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'frame';

    /** @var array */
    private $options = [
        'fields' => [],
    ];

    public function apply(array $item): array
    {
        $fields = $this->getOption('fields');
        // array keys listed
        if (isset($fields[0])) {
            $fields = array_fill_keys($fields, null);
        }

        $item = array_replace_recursive($fields, $item);

        $tmp = [];
        foreach (array_keys($fields) as $field) {
            $tmp[$field] = $item[$field] ?? null;
        }

        return $tmp;
    }
}