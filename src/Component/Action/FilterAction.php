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
        'key' => null,
    ];

    public function apply($item)
    {
        if (false === isset($item[$this->options['key']])) {
            return $item;
        }
        $listItem = $item[$this->options['key']];

        if (is_array($listItem)) {
            $item[$this->options['key']] = array_filter($listItem,function ($itemValue) {
                return $this->hasMatch($itemValue) !== true;
            });
        }

        if (is_string($listItem) && $this->hasMatch($listItem)) {
            $item[$this->options['key']] = null;
            return $item;
        }

        return $item;
    }

    private function hasMatch($listItem)
    {
        return strpos($listItem, $this->options['match']) === false;
    }
}