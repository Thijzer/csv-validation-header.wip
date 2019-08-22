<?php

namespace Misery\Component\Csv\Validator;

use Misery\Component\Csv\Reader\ReaderInterface;
use Misery\Component\Validator\AbstractValidator;
use Misery\Component\Validator\ValidationCollector;

class ReferencedColumnValidator extends AbstractValidator
{
    private $dataStream;

    public function __construct(ValidationCollector $collector, ReaderInterface $dataStream)
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
