<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Modifier\CellModifier;
use Misery\Component\Common\Modifier\RowModifier;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\Registry;

class ModifierAction implements OptionsInterface
{
    use OptionsTrait;

    public const NAME = 'modify';

    /** @var RowModifier|CellModifier */
    private $modifier;

    private $registry;

    /** @var array */
    private $options = [
        'modifier' => null,
        'keys' => null,
    ];

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function apply($item)
    {
        // this should be part of the prepare state
        // when we set the options
        // so don't need to check on every action::apply
        $keys = explode(',',$this->options['keys']);

        foreach ($keys as $key) {
            $listItem = $item[$key] ?? null;
            /** @var RowModifier|CellModifier $modifier */
            if ($listItem && $modifier = $this->getModifier()) {
                if (is_array($listItem)) {
                    $item[$key] = array_map(function ($itemValue) use ($modifier) {
                        return is_string($itemValue) ? $modifier->modify($itemValue) : null;
                    }, $listItem);
                }
                if (is_string($listItem)) {
                    $item[$key] = $modifier->modify($listItem);
                }
            }
        }

        return $item;
    }

    private function getModifier()
    {
        if (null === $this->modifier) {
            if ($this->modifier = $this->registry->filterByAlias($this->options['modifier'])) {
                if ($this->modifier instanceof OptionsInterface) {
                    $this->modifier->setOptions($this->options);
                }
            }
        }

        return $this->modifier;
    }
}
