<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;
use Misery\Component\Source\SourceFilter;

class BindAction implements OptionsInterface
{
    use OptionsTrait;

    private $mapper;

    public const NAME = 'bind';

    /** @var array */
    private $options = [
        'list' => [],
        'filter' => null,
    ];

    # Usage

    public function apply(array $item): array
    {
        /** @var SourceFilter $filter */
        $filter = $this->getOption('filter');

        // don't hardcode values // auto level into the array
        foreach ($this->getOption('list') as $columnKey) {
            if (array_key_exists($columnKey, $item)) {
                $item[$columnKey] = $filter->filter($item);
                if (count($item[$columnKey]) <= 1) {
                    $item[$columnKey] = current($item[$columnKey]);
                }
            }
        }

        return $item;
    }
}