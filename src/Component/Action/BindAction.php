<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;
use Misery\Component\Source\SourceFilter;

class BindAction implements OptionsInterface, ItemReaderAwareInterface
{
    use OptionsTrait;
    use ItemReaderAwareTrait;

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

        if (isset($item['values'])) {
            foreach ($this->getOption('list') as $columnKey) {
                if (array_key_exists($columnKey, $item['values'])) {
                    foreach ($item['values'][$columnKey] as $i => $a) {
                        if (isset($a['data'])) {
                            $item['values'][$columnKey][$i]['data'] = $filter->filter(['code' => $a['key']])->getIterator()->current();
                        }
                    }
                }
            }
        } else {
            foreach ($this->getOption('list') as $columnKey) {
                if (array_key_exists($columnKey, $item)) {
                    $item[$columnKey] = $filter->filter($item);
                }
            }
        }

        return $item;
    }
}