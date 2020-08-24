<?php

namespace Misery\Component\Compare;

use Misery\Component\Common\Functions\ArrayFunctions as Arr;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Reader\ItemReaderInterface;

class ItemCompare
{
    public const ADDED = 'ADDED';
    public const REMOVED = 'REMOVED';
    public const CHANGED = 'CHANGED';

    /** @var ItemReaderInterface */
    private $old;
    /** @var ItemReaderInterface */
    private $new;
    /** @var array|null*/
    private $excludes;

    public function __construct(ItemReaderInterface $old, ItemReaderInterface $new, array $excludes = null)
    {
        $this->old = $old;
        $this->new = $new;
        $this->excludes = $excludes;
    }

    public function compare(string...$references): array
    {
        if (\count($references) > 1) {
            $oldCodes = ReferenceBuilder::build($this->old, ...$references);
            $reference = key($oldCodes);
            $oldCodes = current($oldCodes);
            $newCodes = ReferenceBuilder::buildValues($this->new, ...$references);
        } else {
            $reference = current($references);
            // compare the old with the new
            $oldCodes = ReferenceBuilder::build($this->old, $reference)[$reference];
            $newCodes = ReferenceBuilder::build($this->new, $reference)[$reference];
        }

        $changes = [
            self::ADDED => array_diff($newCodes, $oldCodes),
            self::REMOVED => array_diff($oldCodes, $newCodes),
            self::CHANGED => [],
        ];

        $pointers = array_diff($oldCodes, $changes[self::REMOVED]);

        // flip codes so we can get find the NEW $lineNumber
        $codes = array_flip($newCodes);

        foreach ($this->old->index(array_keys($pointers))->getIterator() as $lineNumber => $old) {
            $id = $oldCodes[$lineNumber];

            $new = current($this->new->index([$codes[$id]])->getItems());

            if ($this->excludes) {
                foreach ($this->excludes as $exclude) {
                    unset($old[$exclude]);
                    unset($new[$exclude]);
                }
            }

            if ($new != $old) {
                $changes[self::CHANGED][$id] = [
                    'reference' => $reference,
                    $reference => $id,
                    'line_number' => $lineNumber,
                    'changes' => array_filter([
                        self::REMOVED => Arr::multiCompare($new, $old),
                        self::ADDED => Arr::multiCompare($old, $new),
                    ]),
                ];
            }
        }

        return $changes;
    }
}