<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Converter\Matcher;

class CopyAction implements ActionInterface, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'copy';

    /** @var array */
    private $options = [
        'from' => null,
        'to' => null,
    ];

    public function apply(array $item): array
    {
        $matched = $this->findMatchedValueData($item, $this->options['from']);
        if ($matched) {
            $matcher = $item[$matched]['matcher']->duplicateWithNewKey($this->options['to']);
            $item[$matcher->getMainKey()] = $item[$matched];
            $item[$matcher->getMainKey()]['matcher'] = $matcher;

            return $item;
        }

        if (!isset($item[$this->options['from']])) {
            return $item;
        }

        $item[$this->options['to']] = $item[$this->options['from']];

        return $item;
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