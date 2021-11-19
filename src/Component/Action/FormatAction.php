<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;
use Misery\Component\Source\SourceFilter;

class FormatAction implements OptionsInterface, ItemReaderAwareInterface
{
    use OptionsTrait;
    use ItemReaderAwareTrait;

    private $mapper;

    public const NAME = 'format';

    /** @var array */
    private $options = [
        'field' => null,
        'functions' => [],
    ];

    public function apply(array $item): array
    {
        $functions = $this->getOption('functions');
        $field = $this->getOption('field');

        // type validation
        if (!isset($item[$field])) {
            return $item;
        }

        foreach ($functions as $function) {
            switch ($function) {
                case 'explode':
                    $item[$field] = explode($this->getOption('separator'), $item[$field]);
                    break;
                case 'select_index':
                    $item[$field] = $item[$field][$this->getOption('index')];
                    break;
                default:
                    break;
            }
        }

        return $item;
    }
}