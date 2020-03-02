<?php

namespace Misery\Component\Validator;

use Misery\Component\Encoder\Validator\Constraint;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;

class UniqueValueValidator extends AbstractValidator implements ItemReaderAwareInterface
{
    use ItemReaderAwareTrait;

    public const NAME = 'unique';

    public function validate($columnName, array $context = []): void
    {
        $columnData = ReferenceBuilder::build($this->getReader(), $columnName)[$columnName];

        $duplicates = array_unique($columnData);
        if (\count($columnData) !== \count($duplicates)) {
            $this->getValidationCollector()->collect(
                new \Misery\Component\Validator\Constraint\UniqueValueConstraint(),
                sprintf(
                    \Misery\Component\Validator\Constraint\UniqueValueConstraint::UNIQUE_VALUE,
                    implode(', ', array_unique(array_diff_assoc($columnData, $duplicates)))
                ),
                $context
            );
        }
    }
}
