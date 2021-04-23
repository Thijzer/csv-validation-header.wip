<?php

namespace Misery\Component\Common\Generator;

interface GeneratorInterface
{
    public function generate(...$data): string;
}