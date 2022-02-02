<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Mapping\ColumnMapper;

class KeyMapperAction implements OptionsInterface
{
    use OptionsTrait;

    private $mapper;

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
            $item[$key] = $this->mapper->map($item[$key], $this->getOption('list'));
            return $item;
        }

        return $this->mapper->map($item, $this->getOption('list'));
    }
}