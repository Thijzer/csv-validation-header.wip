<?php

namespace Misery\Component\Common\Cursor;

interface CursorInterface extends \Countable, \SeekableIterator
{
    public function loop(callable $callable): void;
}