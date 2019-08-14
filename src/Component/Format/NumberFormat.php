<?php

namespace RFC\Component\Format;

class NumberFormat
{
    public function format($value)
    {
        return number_format($value);
    }
}