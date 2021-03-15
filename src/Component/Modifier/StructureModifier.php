<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\RowModifier;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Converter\AkeneoCsvStructureConverter;

class StructureModifier implements RowModifier, OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'structure';

    private $options = [
        'classFQN' => AkeneoCsvStructureConverter::class,
        'codes' => [],
    ];

    /** @inheritDoc */
    public function modify(array $item): array
    {
        return $this->options['classFQN']->convert($item, $this->options['codes']);
    }

    /** @inheritDoc */
    public function reverseModify(array $item): array
    {
        return $this->options['classFQN']->revert($item, $this->options['codes']);
    }
}
