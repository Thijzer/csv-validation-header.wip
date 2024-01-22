<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class ListMapperAction implements ActionInterface, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'value_mapping_in_list';

    /** @var array */
    private $options = [
        'field' => null,
        'list' => [],
    ];

    public function apply(array $item): array
    {
        $value = $item[$this->options['field']] ?? null;
        if ($value && array_key_exists($value, $this->options['list'])) {
            $item[$this->options['field']] = $this->options['list'][$value];
        }

        return $item;
    }
}