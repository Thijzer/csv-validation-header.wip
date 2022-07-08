<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Mapping\ColumnMapper;

class GenerateIdAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'generate_id';

    /** @var array */
    private $options = [
        'id' => 1,
        'field' => null,
    ];

    public function apply(array $item): array
    {
        $id = $this->getOption('id');

        $item[$this->getOption('field')] = $id;

        $id = $id+1;
        $this->setOption('id', $id);

        return $item;
    }
}