<?php

namespace Misery\Component\Common\Cursor;

class ContinuousBufferFetcher
{
    private string $indexReference;
    private array $buffer = [];
    private CursorInterface $cursor;
    private ZoneIndexer $indexer;

    public function __construct(CursorInterface $cursor, string $indexReference)
    {
        $this->indexer = new ZoneIndexer();
        $this->cursor = $cursor;
        $this->indexReference = $indexReference;
    }

    public function get(string $reference)
    {
        $this->indexer->init($this->cursor, $this->indexReference);

        $index = $this->indexer->getIndexByReference($reference);
        if (null === $index) {
            return false;
        }

        $zone = $this->indexer->getZoneByIndex($index);
        if (false === $this->itemInBuffer($index, $zone)) {
            // new item to load in
            $this->loadBufferFromZone($zone);

            // only keep 3 ranges, unset the first one
            if (count($this->buffer) > 3) {
                $first = current(array_keys($this->buffer));
                unset($this->buffer[$first]);
            }
        }

        // clear memory
        $item = $this->buffer[$zone][$index] ?? false;
        unset($this->buffer[$zone][$index]);
        $this->indexer->depleteIndex($reference, $index);

        return $item;
    }

    private function itemInBuffer(int $index, int $zone): bool
    {
        return isset($this->buffer[$zone][$index]);
    }

    private function loadBufferFromZone(int $zone): void
    {
        $range = $this->indexer->getRangeFromZone($zone);

        $this->cursor->seek(current($range));
        while ($row = $this->cursor->current()) {
            $this->buffer[$zone][$this->cursor->key()] = $row;
            if (\count($this->buffer[$zone]) === \count($range)) {
                break;
            }
            $this->cursor->next();
        }
    }
}