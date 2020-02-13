<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\CellModifier;

class UTF8EncodeModifier implements CellModifier
{
    const UTF8 = 'UTF-8';

    public function modify(string $value)
    {
        return mb_detect_encoding($value, self::UTF8, true) ? $value : utf8_encode($value);
    }
}