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

    /** @var array */
    private $context = [];

    /** @inheritDoc */
    public function validate($value, array $context = []): void
    {
        $this->context = $context;

        if(is_array($value))
        {
            $this->validateArray($value);
        }
        else
        {
            $this->validateValue($value);
        }
    }

    private function validateArray(array $array): void
    {
        foreach($array as $value)
        {
            $this->validateValue($value);
        }
    }

    private function validateValue($value): void
    {
        if (!empty($this->options['options']) && !\in_array($value, $this->options['options'], true)) {
            $this->getValidationCollector()->collect(
                new Constraint\InArrayConstraint(),
                sprintf(Constraint\InArrayConstraint::NOT_LISTED, $value),
                $this->context
            );
        }
    }
}