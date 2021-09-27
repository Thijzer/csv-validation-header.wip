<?php

namespace Misery\Component\Common\Options;

trait OptionsTrait
{
    public function setOptions(array $options = []): void
    {
        $this->options = array_merge($this->options, $options);
    }

    public function setOption($key, $option): void
    {
        $this->options[$key] = $option;
    }

    public function getOption(string $key)
    {
        return $this->options[$key] ?? null;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}