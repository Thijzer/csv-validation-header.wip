<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Mapping\ColumnMapper;

class RenameAction implements OptionsInterface
{
    use OptionsTrait;

    private $mapper;

    public const NAME = 'rename';

    public function __construct()
    {
        $this->mapper = new ColumnMapper();
    }

    /** @var array */
    private $options = [
        'from' => null,
        'to' => null,
        'suffix' => null,
        'exclude_list' => [],
        'filter_list' => null,
        'fields' => [],
    ];

    public function apply(array $item): array
    {
        $from = $this->getOption('from');
        $to = $this->getOption('to');

        if (!empty($this->options['suffix'])) {
            return $this->mapper->mapWithSuffix(
                $item,
                $this->options['suffix'],
                $this->options['exclude_list'],
                $this->options['filter_list'] ?? $this->options['fields'],
            );
        }

        $fields = $this->getOption('fields');
        if ($fields !== []) {
            return $this->mapper->map($item, $fields);
        }

        return $this->mapper->map($item, [$from => $to]);
    }
}