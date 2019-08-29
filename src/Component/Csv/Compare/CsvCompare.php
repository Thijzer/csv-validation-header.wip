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

    public function compare(string $reference, array $options): array
    {
        $oldCodes = $this->old->getColumn($reference);
        $newCodes = $this->new->getColumn($reference);

        $changes = [
            self::ADDED => array_diff($newCodes, $oldCodes),
            self::CHANGED => [],
            self::REMOVED => array_diff($oldCodes, $newCodes),
        ];

        // filter out created and removed lines
        $otherCodes = array_diff($oldCodes, $changes[self::ADDED], $changes[self::REMOVED]);

        foreach ($otherCodes as $lineNumber => $id) {
            $old = $this->new->findOneBy([$reference => $id], $options);
            $new = $this->old->findOneBy([$reference => $id], $options);

            if ($new != $old) {
                $changes[self::CHANGED][] = [
                    'reference' => $reference,
                    'id' => $id,
                    'line' => $lineNumber,
                    'changes' => array_filter([
                        self::REMOVED => Arr::multiCompare($old, $new),
                        self::ADDED => Arr::multiCompare($new, $old),
                    ]),
                ];
            }
        }

        return $changes;
    }
}