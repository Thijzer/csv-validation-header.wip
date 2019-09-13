<?php

namespace Misery\Component\Csv\Combine;

use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Common\Processor\NullDataProcessor;
use Misery\Component\Csv\Compare\CsvCompare;
use Misery\Component\Csv\Reader\ReaderInterface;

class CsvCombine
{
    private $shouldDiffer = false;

    public function combineInto(ReaderInterface $readerA, ReaderInterface $readerB, string $reference, callable $call): void
    {
        $csvCompare = new CsvCompare($readerA, $readerB);

        $differences = $csvCompare->compare($reference);

        $combinedHeaders = ArrayFunctions::arrayUnion(
            array_keys($readerA->getCursor()->current()),
            array_keys($readerB->getCursor()->current())
        );
        $combinedHeaderRow = array_combine($combinedHeaders, array_fill(0, \count($combinedHeaders), null));

        // TODO replace with ProcessorAware
        if (method_exists($readerA->getCursor(), 'setProcessor')) {
            $readerA->getCursor()->setProcessor(new NullDataProcessor());
        }
        if (method_exists($readerB->getCursor(), 'setProcessor')) {
            $readerB->getCursor()->setProcessor(new NullDataProcessor());
        }

        if (false === $this->shouldDiffer) {
            $readerA->loop(function ($row) use ($call, $reference, $combinedHeaderRow, $differences) {
                if (!array_key_exists($row[$reference], $differences[CsvCompare::CHANGED])) {
                    $call(array_merge($combinedHeaderRow, $row));
                }
            });
        }

        foreach ($differences as $type => $difference) {
            if (CsvCompare::ADDED === $type) {
                $row = $readerB->findOneBy([$reference => current($difference)]);
                $call(array_merge($combinedHeaderRow, $row));
            }
            if (CsvCompare::CHANGED === $type) {
                foreach ($difference as $diff) {
                    $row = $readerB->findOneBy([$reference => $diff[$reference]]);
                    $call(array_merge($combinedHeaderRow, $row));
                }
            }
        }
    }

    public function join(ReaderInterface $readerA, ReaderInterface $readerB, string $reference, callable $call): void
    {
        if (empty($readerA->getCursor()->current())) {
            $readerB->loop(function ($row) use ($call) {
                $call($row);
            });
            $readerA->reset($readerA->getCursor());
            return;
        }

        $this->differInto($readerA, $readerB, $reference, $call);
    }

    public function differInto(ReaderInterface $readerA, ReaderInterface $readerB, string $reference, callable $call): void
    {
        $this->shouldDiffer = true;

        $this->combineInto($readerA, $readerB, $reference, $call);

        $this->shouldDiffer = false;
    }
}
