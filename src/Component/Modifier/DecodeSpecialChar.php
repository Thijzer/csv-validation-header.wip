<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\SpecialModifier;

class DecodeSpecialChar implements SpecialModifier
{
    const NAME = 'decode_special_char';

    public function modify(string $value, int $flag = ENT_QUOTES)
    {
        return html_entity_decode($value,$flag);
    }
}