<?php

namespace Misery\Component\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Encoder\Validator\Constraint;
use Misery\Component\Item\Builder\ReferenceBuilder;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;

class ReferenceExistValidator extends AbstractValidator implements ItemReaderAwareInterface, OptionsInterface
{
    use OptionsTrait;
    use ItemReaderAwareTrait;

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

        $referencedValues = ReferenceBuilder::build(
            $this->getReader(),
            $this->options['reference']
        )[$this->options['reference']];

        if (!\in_array($cellValue, $referencedValues, true)) {
            $this->getValidationCollector()->collect(
                new \Misery\Component\Validator\Constraint\ReferencedColumnConstraint(),
                sprintf(
                    \Misery\Component\Validator\Constraint\ReferencedColumnConstraint::UNKNOWN_REFERENCE,
                    $this->options['reference'],
                    $cellValue,
                    $this->options['file']
                ),
                $context
            );
        }
    }
}
