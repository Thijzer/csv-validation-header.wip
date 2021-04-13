<?php

namespace Misery\Component\Common\Picker;

interface ValuePickerInterface
{
    public static function pick(array $item, string $key, array $context = []);
}