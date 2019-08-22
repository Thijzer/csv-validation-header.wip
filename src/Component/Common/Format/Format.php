<?php

namespace Misery\Component\Common\Format;

interface Format
{
    /**
     * @param string|array $value
     *
     * @return array
     */
    public function format($value);

    public function reverseFormat($value);
}
