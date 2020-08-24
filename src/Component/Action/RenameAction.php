<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Mapping\ColumnMapper;
use Misery\Component\Mapping\Mapper;
use Misery\Component\Reader\ReaderAwareInterface;
use Misery\Component\Reader\ReaderAwareTrait;

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
    ];

    public function apply(array $item): array
    {
        return $this->mapper->map($item, [$this->options['from'] => $this->options['to']]);
    }
}