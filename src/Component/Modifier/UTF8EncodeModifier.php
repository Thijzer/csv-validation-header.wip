<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\Modifier;

class UTF8EncodeModifier implements Modifier
{
    const UTF8 = 'UTF-8';

    public function modify($value)
    {
        return mb_detect_encoding($value, self::UTF8, true) ? $value : utf8_encode($value);
    }
}