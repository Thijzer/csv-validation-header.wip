<?php

namespace Misery\Component\Csv\Compare;

use Misery\Component\Common\Functions\ArrayFunctions as Arr;
use Misery\Component\Csv\Reader\CsvReaderInterface;

class CsvCompare
{
    public const ADDED = 'ADDED';
    public const REMOVED = 'REMOVED';
    public const CHANGED = 'CHANGED';

    private $old;
    private $new;
    /**
     * @var array
     */
    private $excludes;

    public function __construct(CsvReaderInterface $old, CsvReaderInterface $new, array $excludes = null)
    {
        $this->old = $old;
        $this->new = $new;
        $this->excludes = $excludes;
    }

    public function compare(string...$references): array
    {
        if (\count($references) === 2) {
            $oldCodes = $this->old->indexColumnsReference(...$references);
            $reference = key($oldCodes);
            $oldCodes = current($oldCodes);
            $newCodes = current($this->new->indexColumnsReference(...$references));
        } else {
            $reference = current($references);
            // compare the old with the new
            $oldCodes = $this->old->getColumnNames($reference)->getValues()[$reference];
            $newCodes = $this->new->getColumnNames($reference)->getValues()[$reference];
        }

        $changes = [
            self::ADDED => array_diff($newCodes, $oldCodes),
            self::REMOVED => array_diff($oldCodes, $newCodes),
            self::CHANGED => [],
        ];

        $possibleChanges = array_diff($oldCodes, $changes[self::REMOVED]);

        // flip codes so we can get find the NEW $lineNumber
        $codes = array_flip($newCodes);

        foreach ($this->old->getRows(array_keys($possibleChanges)) as $lineNumber => $old) {
            $id = $oldCodes[$lineNumber];
            $new = current($this->new->getRow($codes[$id])->getValues());

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