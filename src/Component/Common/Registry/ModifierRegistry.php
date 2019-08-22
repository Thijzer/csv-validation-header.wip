<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Modifier\Modifier;

class ModifierRegistry implements Registry
{
    public const NAME = 'modifier';

    private $modifiers;

    public function __construct()
    {
        $this->modifiers = new ArrayCollection();
    }

    public function register(Modifier $modify): self
    {
        $this->modifiers->set(\get_class($modify), $modify);

        return $this;
    }

    public function filterByName($name): ArrayCollection
    {
        return $this->modifiers->filter(static function (Modifier $modifier) use ($name) {
            return $modifier::NAME === $name;
        });
    }
}