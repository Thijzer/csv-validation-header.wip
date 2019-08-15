<?php

namespace Component\Csv\Validator;

use Component\Csv\Reader\CsvParserInterface;
use Component\Validator\AbstractValidator;
use Component\Validator\ValidationCollector;

class ReferencedColumnValidator extends AbstractValidator
{
    private $dataStream;

    public function __construct(ValidationCollector $collector, CsvParserInterface $dataStream)
    {
        parent::__construct($collector);
        $this->dataStream = $dataStream;
    }

    public function validate($cellValue, array $options = []): void
    {
        $columnName = $options['column'];

        $this->dataStream->indexColumn($columnName);

        if (!\in_array($cellValue, $this->dataStream->getColumn($columnName), true)) {
            $this->getCollector()->collect(
                new Constraint\ReferencedColumnConstraint(),
                Constraint\ReferencedColumnConstraint::UNKNOWN_REFERENCE
            );
        }
    }
}
