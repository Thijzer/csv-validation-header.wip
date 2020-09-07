<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\CellModifier;

class ValueDecodeSpecialChar implements CellModifier
{
    const UTF8 = 'decode_special_char';

    public function modify(string $value)
    {
        return html_entity_decode($value,ENT_QUOTES);
    }
}