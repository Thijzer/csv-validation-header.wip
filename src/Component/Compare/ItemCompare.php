<?php

namespace Misery\Component\Compare;

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Common\Functions\ArrayFunctions as Arr;
use Misery\Component\Filter\ColumnReducer;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Reader\ItemReader;

class ItemCompare
{
    public const ADDED = 'ADDED';
    public const REMOVED = 'REMOVED';
    public const CHANGED = 'CHANGED';

    /** @var CursorInterface */
    private $master;
    /** @var CursorInterface */
    private $branch;
    /** @var array|null*/
    private $excludes;

    public function __construct(CursorInterface $master, CursorInterface $branch, array $excludes = null)
    {
        $this->master = $master;
        $this->branch = $branch;
        $this->excludes = $excludes;
    }

    public function compare(string...$references): array
    {
        $this->branch->rewind();
        $this->master->rewind();

        $headersMaster = array_keys($this->master->current());
        $headersBranch = array_keys($this->branch->current());

        $headers = [
            'out_of_alignment' => [
                self::ADDED => array_diff($headersBranch, $headersMaster),
                self::REMOVED => array_diff($headersMaster, $headersBranch),
            ],
        ];

        $comparableHeaders = array_diff($headersBranch, ...array_values($headers['out_of_alignment']));

        $masterReader = new ItemReader($this->master instanceof CachedCursor ? $this->master : CachedCursor::create($this->master));
        $branchReader = new ItemReader($this->branch instanceof CachedCursor ? $this->branch : CachedCursor::create($this->branch));

        if (\count($references) > 1) {
            $masterCodes = ReferenceBuilder::build($masterReader, ...$references);
            $reference = key($masterCodes);
            $masterCodes = current($masterCodes);
            $branchCodes = ReferenceBuilder::buildValues($branchReader, ...$references);
        } else {
            $reference = current($references);
            // compare the master with the branch
            $masterCodes = ReferenceBuilder::buildValues($masterReader, $reference);
            $branchCodes = ReferenceBuilder::buildValues($branchReader, $reference);
        }

        $changes = [
            'headers' => $headers,
            'items' => [
                self::ADDED => array_diff($branchCodes, $masterCodes),
                self::REMOVED => array_diff($masterCodes, $branchCodes),
                self::CHANGED => [],
            ],
        ];

        $pointers = array_diff($masterCodes, $changes['items'][self::REMOVED]);

        // flip codes so we can get find the NEW $lineNumber
        $codes = array_flip($branchCodes);

        foreach ($masterReader->index(array_keys($pointers))->getIterator() as $lineNumber => $master) {
            $id = $masterCodes[$lineNumber];
            $branch = current($branchReader->index([$codes[$id]])->getItems());
            $master = ColumnReducer::reduceItem($master, ...$comparableHeaders);
            $branch = ColumnReducer::reduceItem($branch, ...$comparableHeaders);

            if ($this->excludes) {
                foreach ($this->excludes as $exclude) {
                    unset($master[$exclude]);
                    unset($branch[$exclude]);
                }
            }

            if ($branch != $master) {
                $changes['items'][self::CHANGED][$id] = [
                    'reference' => $reference,
                    $reference => $id,
                    'line_number' => $lineNumber,
                    'changes' => array_filter([
                        self::REMOVED => Arr::multiCompare($branch, $master),
                        self::ADDED => Arr::multiCompare($master, $branch),
                    ]),
                ];
            }
        }

        return $changes;
    }
}