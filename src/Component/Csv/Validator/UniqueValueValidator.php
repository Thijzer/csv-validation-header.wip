<?php

namespace Component\Csv\Validator;

use Component\Csv\Reader\CsvParserInterface;
use Component\Validator\AbstractValidator;
use Component\Validator\ValidationCollector;

class UniqueValueValidator extends AbstractValidator
{
    private $dataStream;

    public function __construct(ValidationCollector $collector, CsvParserInterface $dataStream)
    {
        parent::__construct($collector);
        $this->dataStream = $dataStream;
    }

    public function validate($columnName, array $options = []): void
    {
        $this->dataStream->indexColumn($columnName);

        $columnData = $this->dataStream->getColumn($columnName);

        if (count($columnData) !== count(array_unique($columnData))) {
            $this->getCollector()->collect(
                new Constraint\UniqueValueConstraint(),
                Constraint\UniqueValueConstraint::UNIQUE_VALUE
            );
        }
    }
}
