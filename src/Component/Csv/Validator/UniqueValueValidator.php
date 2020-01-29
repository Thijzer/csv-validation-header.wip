<?php

namespace Misery\Component\Csv\Validator;

use Misery\Component\Csv\Fetcher\ColumnValuesFetcher;
use Misery\Component\Csv\Reader\RowReaderAwareInterface;
use Misery\Component\Csv\Reader\RowReaderAwareTrait;
use Misery\Component\Validator\AbstractValidator;

class UniqueValueValidator extends AbstractValidator implements RowReaderAwareInterface
{
    use RowReaderAwareTrait;

    public const NAME = 'unique';

    public function validate($columnName, array $context = []): void
    {
        $columnData = ColumnValuesFetcher::fetchValues($this->getReader(), $columnName);

        $duplicates = array_unique($columnData);
        if (\count($columnData) !== \count($duplicates)) {
            $this->getValidationCollector()->collect(
                new Constraint\UniqueValueConstraint(),
                sprintf(
                    Constraint\UniqueValueConstraint::UNIQUE_VALUE,
                    implode(', ', array_unique(array_diff_assoc($columnData, $duplicates)))
                ),
                $context
            );
        }
    }
}
