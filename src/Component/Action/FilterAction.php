<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class FilterAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'filter';

    /** @var array */
    private $options = [
        'match' => null,
        'equals' => null,
        'key' => null,
        'field' => null,
        'case-sensitive' => false,
    ];

    public function apply($item)
    {
        $field = $this->getOption('key', $this->getOption('field')); # legacy support
        $listItem = $item[$field] ?? null;
        if (empty($listItem) || empty($field)) {
            return $item;
        }

        if ($this->getOption('match')) {
            if (is_array($listItem)) {
                $item[$field] = array_filter($listItem, function ($itemValue) {
                    return $this->hasMatch($itemValue) !== true;
                });
            }

            if (is_string($listItem) && $this->hasMatch($listItem)) {
                $item[$field] = null;
                return $item;
            }
        }

        if ($this->getOption('equals')) {
            if (is_array($listItem)) {
                $item[$field] = array_filter($listItem, function ($itemValue) {
                    return $this->equals($itemValue) !== true;
                });
            }

            if (is_string($listItem) && $this->equals($listItem)) {
                $item[$field] = null;
                return $item;
            }
        }

        return $item;
    }

    private function equals(string $listItem): bool
    {
        return $this->options['case-sensitive'] ?
            $listItem === $this->options['equals'] :
            strtolower($listItem) === strtolower($this->options['equals']);
    }

    private function hasMatch($listItem): bool
    {
        return $this->options['case-sensitive'] ?
            strpos($listItem, $this->options['match']) === false :
            strpos(strtolower($listItem), strtolower($this->options['match'])) === false;
    }
}