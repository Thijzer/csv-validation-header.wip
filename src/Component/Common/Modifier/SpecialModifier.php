<?php

namespace Misery\Component\Common\Modifier;

interface SpecialModifier extends Modifier
{
    public function modify(string $value, int $flag);
}