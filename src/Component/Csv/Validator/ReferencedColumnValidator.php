<?php

namespace Misery\Component\Csv\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Csv\Fetcher\ColumnValuesFetcher;
use Misery\Component\Csv\Reader\RowReaderAwareInterface;
use Misery\Component\Csv\Reader\RowReaderAwareTrait;
use Misery\Component\Validator\AbstractValidator;

class ReferencedColumnValidator extends AbstractValidator implements RowReaderAwareInterface, OptionsInterface
{
    use OptionsTrait;
    use RowReaderAwareTrait;

    public const NAME = 'reference_exist';

    private $options = [
        'reader' => null,
        'file' => null,
        'reference' => null,
    ];

    public function validate($cellValue, array $context = []): void
    {
        if (empty($cellValue)) {
            return;
        }

        if (!\in_array($cellValue, ColumnValuesFetcher::fetch($this->getReader(), $this->options['reference']), true)) {
            $this->getValidationCollector()->collect(
                new Constraint\ReferencedColumnConstraint(),
                sprintf(
                    Constraint\ReferencedColumnConstraint::UNKNOWN_REFERENCE,
                    $this->options['reference'],
                    $cellValue,
                    $this->options['file']
                ),
                $context
            );
        }
    }
}
