<?php

namespace Misery\Component\Common\Options;

interface OptionsInterface
{
    public function setOptions(array $options = []): void;
    public function getOption(string $key);
    public function getOptions(): array;
}