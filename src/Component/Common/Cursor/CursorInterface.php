<?php

namespace Misery\Component\Common\Cursor;

interface CursorInterface extends \Countable, \SeekableIterator
{
    /**
     * Iterate over items  with a Generator and do something
     * @param callable $callable
     */
    public function loop(callable $callable): void;

    /**
     * Iterate over items with a Generator
     *
     * @return \Generator
     */
    public function getInterator(): \Generator;
}