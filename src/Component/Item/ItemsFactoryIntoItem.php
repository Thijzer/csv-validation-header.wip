<?php

namespace Misery\Component\Item;

use Misery\Component\Statement\WhenStatementBuilder;

class ItemsFactoryIntoItem
{
    public static function createFromConfig(array $items, array $configuration): array
    {
        $result = [];
        $main = [];
        foreach ($items as $item) {
            $main = $item;
            foreach ($configuration as $code => $options) {
                if (isset($options['when'])) {
                    $statement = WhenStatementBuilder::buildFromOperator($options['when']['operator']);
                    $statement->when($options['when']['field'], $options['when']['state']);
                    if ($statement->isApplicable($item)) {
                        $result[$code][] = $item[$options['value']];
                    }
                    continue;
                }
                $result[$code][] = $item[$options['value']];
            }
        }

        return array_merge($result, $main);
    }
}