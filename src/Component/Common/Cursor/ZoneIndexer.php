<?php

namespace Misery\Component\Common\Cursor;

class ZoneIndexer
{
    private const MEDIUM_CACHE_SIZE = 10000;

    private array $indexes = [];
    private array $zones;

    public function init(CursorInterface $cursor, string $reference): void
    {
        if ($this->indexes === []) {
            // prep indexes
            $cursor->loop(function ($row) use ($cursor, $reference) {
                if ($row) {
                    $index = (int) $cursor->key();
                    $zone = (int) (($index -1) / self::MEDIUM_CACHE_SIZE);
                    $referenceValue = $row[$reference];
                    $this->indexes[crc32($referenceValue)] = $index;
                    $this->zones[$index] = $zone;
                }
            });
            $cursor->rewind();
        }
    }

    public function depleteIndex(string $reference, int $index): void
    {
        unset($this->zones[$index]);
        unset($this->indexes[crc32($reference)]);
    }

    public function getIndexByReference(string $reference)
    {
        return $this->indexes[crc32($reference)] ?? null;
    }

    public function getZoneByIndex(int $index)
    {
        return $this->zones[$index] ?? null;
    }

    public function getRangeFromZone(int $zone): array
    {
        $keys = [];
        foreach ($this->zones as $key => $linkedZone) {
            if ($linkedZone === $zone) {
                $keys[] = $key;
            }
        }

        return $keys;
    }
}
