<?php

namespace Misery\Component\Validator;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;

class InArrayValidator extends AbstractValidator implements OptionsInterface
{
    public const NAME = 'in_array';

    use OptionsTrait;

    private $options = [
        'options' => [],
    ];

    public function validate($value, array $context = []): void
    {
        if (!empty($this->options['options']) && !\in_array($value, $this->options['options'], true)) {
            $this->getValidationCollector()->collect(
                new Constraint\InArrayConstraint(),
                sprintf(Constraint\InArrayConstraint::NOT_LISTED, $value),
                $context
            );
        }
    }
}