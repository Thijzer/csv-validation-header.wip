<?php

namespace Component\Filter;

class NullEmptyStringFilter
{
    public function filter(string $value):? string
    {
        return '' === $value ? null : $value;
    }
}
