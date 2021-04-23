<?php

namespace Misery\Component\Common\Pipeline;

interface PipeWriterInterface
{
    public function write(array $data): void;
}