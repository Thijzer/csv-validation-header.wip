<?php

namespace Misery\Component\Common\Pipeline;

class LoggingPipe implements PipeInterface
{

    public function pipe(array $item): array
    {
        dump($item);
        return $item;
    }
}