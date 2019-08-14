<?php

namespace Component\Filter;

class BoolFormat
{
    public function filter(string $value, string $true, string $false):? bool
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