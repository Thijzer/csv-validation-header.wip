<?php

namespace Misery\Component\Writer;

interface ItemWriterInterface
{
    public function write(array $data): void;
}