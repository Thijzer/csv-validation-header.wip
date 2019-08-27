<?php

namespace Misery\Component\Csv\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Csv\Reader\ReaderAwareInterface;
use Misery\Component\Csv\Reader\ReaderAwareTrait;
use Misery\Component\Csv\Reader\ReaderInterface;
use Misery\Component\Validator\AbstractValidator;

class ReferencedColumnValidator extends AbstractValidator implements ReaderAwareInterface,  OptionsInterface
{
    use OptionsTrait;
    use ReaderAwareTrait;

    public const NAME = 'reference_exist';

    private $options = [
        'reader' => null,
        'file' => null,
        'id' => null,
    ];

    public function validate($cellValue, array $context = []): void
    {
        if (empty($cellValue)) {
            return;
        }

        /** @var ReaderInterface $reader */
        $reader = $this->getReader();

        if (!\in_array($cellValue, $reader->getColumn($this->options['id']), true)) {
            $this->getCollector()->collect(
                new Constraint\ReferencedColumnConstraint(),
                sprintf(
                    Constraint\ReferencedColumnConstraint::UNKNOWN_REFERENCE,
                    $this->options['id'],
                    $cellValue,
                    $this->options['file']
                ),
                $context
            );
        }
    }
}
