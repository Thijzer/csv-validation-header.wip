<?php

namespace Misery\Component\Reader;

use Misery\Component\Item\Builder\ReferenceBuilder;

class ItemReader implements ItemReaderInterface
{
    private $cursor;

    public function __construct(\Iterator $cursor)
    {
        $this->cursor = $cursor;
    }

    /** @inheritDoc */
    public function read()
    {
        $item = $this->cursor->current();
        $this->cursor->next();

        return $item;
    }

    public function index(array $lines): ItemReaderInterface
    {
        return new self($this->processIndex($lines));
    }

    private function processIndex(array $lines): \Generator
    {
        foreach ($lines as $lineNr) {
            $this->seek($lineNr);
            yield $lineNr => $this->cursor->current();
        }
    }

    /**
     * Adds seek support for \Iterator objects
     */
    public function seek($pointer): void
    {
        $this->cursor->rewind();
        while ($this->cursor->valid()) {
            if ($this->cursor->key() === $pointer) {
                break;
            }
            $this->cursor->next();
        }

        // @TODO throw outofboundexception
    }

    public function find(array $constraints): ReaderInterface
    {
        $reader = $this;
        foreach ($constraints as $columnName => $rowValue) {
            if (is_string($rowValue)) {
                $rowValue = [$rowValue];
            }
            if ($rowValue === ['UNIQUE']) {
                $reader = new self(
                    $this->processIndex(
                        array_values(
                            array_flip(
                                ReferenceBuilder::build($reader, $columnName)[$columnName]
                            )
                        )
                    )
                );
            } elseif ($rowValue === ['NOT_NULL']) {
                $reader = $reader->filter(static function ($row) use ($rowValue, $columnName) {
                    return false === in_array($row[$columnName], [NULL]);
                });
            } else  {
                $reader = $reader->filter(static function ($row) use ($rowValue, $columnName) {
                    return in_array($row[$columnName], $rowValue);
                });
            }
        }

        return $reader;
    }

    /**
     * PLEASE don't use the sort on very large data sets
     * array_multisort can only sort on the whole array in memory
     *
     */
    public function sort(array $criteria): ReaderInterface
    {
        $flags = ['ASC' => SORT_ASC, 'DSC' => SORT_DESC, 'DESC' => SORT_DESC];
        $setup = [];
        foreach ($criteria as $keyName => $sortDirection) {
            $setup[] = ReferenceBuilder::buildValues($this, $keyName);
            $setup[] = $flags[strtoupper($sortDirection)];
        }
        // should be part of the criteria
        $setup[] = SORT_NUMERIC;
        $setup[] = $this->getItems();

        array_multisort(...$setup);

        return new self(new ItemCollection(end($setup)));
    }

    public function filter(callable $callable): ReaderInterface
    {
        return new self($this->processFilter($callable));
    }

    private function processFilter(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            if (true === $callable($row)) {
                yield $key => $row;
            }
        }
    }

    public function map(callable $callable): ReaderInterface
    {
        return new self($this->processMap($callable));
    }

    private function processMap(callable $callable): \Generator
    {
        foreach ($this->getIterator() as $key => $row) {
            yield $key => $callable($row);
        }
    }

    public function getIterator(): \Iterator
    {
        return $this->cursor;
    }

    public function getItems(): array
    {
        return iterator_to_array($this->cursor);
    }
}
