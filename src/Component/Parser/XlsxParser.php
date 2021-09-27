<?php

namespace Misery\Component\Parser;

use Misery\Component\Common\Cursor\CursorInterface;

class XlsxParser implements CursorInterface
{
    /** @var string */
    private $filename;
    /** @var \Iterator */
    private $cursor;
    /** @var mixed|void */
    private $headers;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public static function create(string $filename): self
    {
        return new self($filename);
    }

    private function init()
    {
        // https://github.com/AsperaGmbH/xlsx-reader
        $reader = new \Aspera\Spreadsheet\XLSX\Reader();
        $reader->open($this->filename);
        $this->cursor = $reader;

        $this->headers = $this->cursor->current();
        $this->next();
    }

    public function current()
    {
        if ($this->cursor === null) {
            $this->init();
        }

        $current = $this->cursor->current();
        if ([] === $current || null === $this->headers) {
            return false;
        }

        // fill dragging empty cells
        $c1 = min(count($this->headers), count($current));

        return @array_combine(
            $this->headers,
            $current+array_fill($c1, count($this->headers)-$c1, null)
        );
    }

    public function next(): void
    {
        $this->cursor->next();
    }

    public function key()
    {
        return $this->cursor->key();
    }

    public function valid(): bool
    {
        return $this->cursor->valid();
    }

    public function rewind(): void
    {
        $this->cursor->rewind();
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    public function loop(callable $callable): void
    {
        // TODO: Implement loop() method.
    }

    public function getIterator(): \Generator
    {
        return $this->cursor;
    }

    public function seek($offset)
    {
        // TODO: Implement seek() method.
    }
}