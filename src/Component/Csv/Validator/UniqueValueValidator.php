<?php

namespace Misery\Component\Csv\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Csv\Reader\ReaderAwareInterface;
use Misery\Component\Csv\Reader\ReaderAwareTrait;
use Misery\Component\Csv\Reader\ReaderInterface;
use Misery\Component\Validator\AbstractValidator;

class UniqueValueValidator extends AbstractValidator implements OptionsInterface, ReaderAwareInterface
{
    use OptionsTrait;
    use ReaderAwareTrait;

    public const NAME = 'unique';

    private $options = [];

    public function validate($columnName, array $context = []): void
    {
        /** @var ReaderInterface $reader */
        $reader = $this->getReader();
        $reader->indexColumn($columnName);

        $columnData = array_filter($reader->getColumn($columnName));

        $duplicates = array_unique($columnData);
        if (\count($columnData) !== \count($duplicates)) {
            $this->getCollector()->collect(
                new Constraint\UniqueValueConstraint(),
                sprintf(
                    Constraint\UniqueValueConstraint::UNIQUE_VALUE,
                    implode(', ', $duplicates)
                ),
                $context
            );
        }
    }
}
