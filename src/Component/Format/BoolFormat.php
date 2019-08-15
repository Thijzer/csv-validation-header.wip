<?php

namespace Component\Format;

class BoolFormat
{
    public function format(string $value, string $true, string $false):? bool
    {
        if ($value === $true) {
            return true;
        }

        if ($value === $false) {
            return false;
        }

        return null;
    }
}