<?php

namespace Misery\Component\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class InArrayValidator extends AbstractValidator implements OptionsInterface
{
    /** @var string */
    public const NAME = 'in_array';

    use OptionsTrait;

    /** @var array */
    private $options = [
        'options' => [],
    ];

    /** @inheritDoc */
    public function validate($value, array $context = []): void
    {
        if (is_array($value)) {
            foreach ($value as $valueItem) {
                $this->validate($valueItem, $context);
            }
        }

        if (!is_string($value) || empty($this->options['options'])) {
            return;
        }

        if (!\in_array($value, $this->options['options'], true)) {
            $this->getValidationCollector()->collect(
                new Constraint\InArrayConstraint(),
                sprintf(Constraint\InArrayConstraint::NOT_LISTED, $value),
                $context
            );
        }
    }
}