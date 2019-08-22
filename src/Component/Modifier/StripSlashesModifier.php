<?php

namespace Misery\Component\Modifier;

use Misery\Component\Common\Modifier\CellModifier;

class StripSlashesModifier implements CellModifier
{
    public const NAME = 'stripslashes';

    public function modify(string $value): string
    {
        return stripslashes($value);
    }
}
