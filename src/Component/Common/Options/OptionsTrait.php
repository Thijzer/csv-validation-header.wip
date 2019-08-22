<?php

namespace Misery\Component\Common\Options;

trait OptionsTrait
{
    public function setOptions(array $options = []): void
    {
        $this->options = array_merge($this->options, $options);
    }
}