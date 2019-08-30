<?php

namespace Misery\Component\Csv\Compare;

use Misery\Component\Common\Functions\ArrayFunctions as Arr;
use Misery\Component\Csv\Reader\CsvReader;
use Misery\Component\Csv\Reader\ReaderInterface;

class CsvCompare
{
    public const ADDED = 'ADDED';
    public const REMOVED = 'REMOVED';
    public const CHANGED = 'CHANGED';

    private $old;
    private $new;

    public function __construct(ReaderInterface $old, ReaderInterface $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    public function compare(string $reference): array
    {
        // compare the old with the new
        $oldCodes = $this->old->getColumn($reference);
        $newCodes = $this->new->getColumn($reference);

        $changes = [
            self::ADDED => array_diff($newCodes, $oldCodes),
            self::CHANGED => [],
            self::REMOVED => array_diff($oldCodes, $newCodes),
        ];

        // filter out created and removed lines
        $otherCodes = array_diff($oldCodes, $changes[self::ADDED], $changes[self::REMOVED]);

        foreach ($this->old->getRows(array_keys($otherCodes)) as $lineNumber => $old) {
            $id = $old[$reference];
            $new = $this->new->findOneBy([$reference => $id]);

            if ($new != $old) {
                $changes[self::CHANGED][] = [
                    'reference' => $reference,
                    'id' => $id,
                    'line' => $lineNumber,
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