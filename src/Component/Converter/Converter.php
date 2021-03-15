<?php

namespace Misery\Component\Converter;

interface Converter
{
    public static function convert(array $item, array $codes);
    public static function revert(array $item, array $codes);
}