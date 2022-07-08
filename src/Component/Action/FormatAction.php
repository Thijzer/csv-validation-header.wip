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
        'decimals' => 4,
        'decimal_sep' => '.',
        'mille_sep' => ',',
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
                case 'replace':
                    $item[$field] = str_replace($this->getOption('search'), $this->getOption('replace'), $item[$field]);
                    break;
                case 'number':
                    $item[$field] = number_format(
                        $item[$field],
                        $this->getOption('decimals'),
                        $this->getOption('decimal_sep'),
                        $this->getOption('mille_sep')
                    );
                    break;
                case 'explode':
                    $item[$field] = explode($this->getOption('separator'), $item[$field]);
                    break;
                case 'select_index':
                    $item[$field] = $item[$field][$this->getOption('index')];
                    break;
                case 'sprintf':
                    $item[$field] = sprintf($this->getOption('format'), $item[$field]);
                    break;
                default:
                    break;
            }
        }

        return $item;
    }
}