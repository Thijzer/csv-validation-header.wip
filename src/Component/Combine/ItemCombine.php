<?php

namespace Misery\Component\Combine;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Csv\Compare\ItemCompare;
use Misery\Component\Reader\ItemReader;

class ItemCombine
{
    private $shouldDiffer = false;

    public function combineInto(CursorInterface $cursorA, CursorInterface $cursorB, string $reference, callable $call): void
    {
        $csvCompare = new ItemCompare(
            $readerA = new ItemReader($cursorA),
            $readerB = new ItemReader($cursorB)
        );

        $differences = $csvCompare->compare($reference);

        $combinedHeaders = ArrayFunctions::arrayUnion(
            array_keys($cursorA->current()),
            array_keys($cursorB->current())
        );
        $combinedHeaderRow = array_combine($combinedHeaders, array_fill(0, \count($combinedHeaders), null));

//        if ($cursorA instanceof ProcessorAwareInterface) {
//            $cursorA->setProcessor(new NullDataProcessor());
//        }
//        if ($cursorB instanceof ProcessorAwareInterface) {
//            $cursorB->setProcessor(new NullDataProcessor());
//        }

        if (false === $this->shouldDiffer) {
            $cursorA->loop(function ($row) use ($call, $reference, $combinedHeaderRow, $differences) {
                if (!array_key_exists($row[$reference], $differences[ItemCompare::CHANGED])) {
                    $call(array_merge($combinedHeaderRow, $row));
                }
            });
        }

        foreach ($readerB->index(array_keys($differences[ItemCompare::ADDED])) as $lineNumber => $row) {
            $call(array_merge($combinedHeaderRow, $row));
        }

        $changedReferences = array_keys($differences[ItemCompare::CHANGED]);
        foreach ($cursorB->getIterator() as $row) {
            if (in_array($row[$reference], $changedReferences)) {
                $call(array_merge($combinedHeaderRow, $row));
            }
        }
    }

    public function join(CursorInterface $cursorA, CursorInterface $cursorB, string $reference, callable $call): void
    {
        if (empty($cursorA->current())) {
            $cursorB->loop(function ($row) use ($call) {
                $call($row);
            });
            $cursorA->rewind();

            return;
        }

        $this->differInto($cursorA, $cursorB, $reference, $call);
    }

    public function differInto(CursorInterface $cursorA, CursorInterface $cursorB, string $reference, callable $call): void
    {
        $this->shouldDiffer = true;

        $this->combineInto($cursorA, $cursorB, $reference, $call);

        $this->shouldDiffer = false;
    }
}