<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Converter\Matcher;
use Misery\Component\Mapping\ColumnMapper;

class KeyMapperAction implements OptionsInterface
{
    use OptionsTrait;

    private ColumnMapper $mapper;

    public const NAME = 'key_mapping';

    public function __construct()
    {
        $this->mapper = new ColumnMapper();
    }

    /** @var array */
    private $options = [
        'key' => null,
        'list' => [],
    ];

    public function apply(array $item): array
    {
        if ($key = $this->getOption('key')) {
            $item[$key] = $this->map($item[$key], $this->getOption('list'));
            return $item;
        }

        $list = $this->getOption('list');
        // when dealing with converted data we need the primary keys
        // we just need to replace these keys
        $newList = [];
        foreach ($list as $key => $value) {
            $newKey = $this->findMatchedValueData($item, $key) ?? $key;
            $newList[$newKey] = $value;
        }

        if (count($newList) > 0) {
            return $this->map($item, $newList);
        }

        return $this->map($item, $list);
    }

    private function map(array $item, array $list)
    {
        try {
            return $this->mapper->map($item, $list);
        } catch (\InvalidArgumentException) {
            return $item;
        }
    }

    private function findMatchedValueData(array $item, string $field): int|string|null
    {
        foreach ($item as $key => $itemValue) {
            $matcher = $itemValue['matcher'] ?? null;
            /** @var $matcher Matcher */
            if ($matcher && $matcher->matches($field)) {
                return $key;
            }
        }

        return null;
    }
}