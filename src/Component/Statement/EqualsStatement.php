<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\ActionInterface;
use Misery\Component\Common\Options\OptionsInterface;

class EqualsStatement
{
    /** @var ActionInterface */
    private $action;
    private $context;
    private $condition;
    private $affect;

    private function __construct() {}

    public static function prepare(ActionInterface $action, array $context = []): self
    {
        $self = new self();
        $self->action = $action;
        $self->context = $context;

        return $self;
    }

    public function when(string $field, string $value): self
    {
        $this->condition = new Field($field, $value);

        return $this;
    }

    public function then(string $field, string $value): void
    {
        $this->affect = new Field($field, $value);
    }

    private function whenField(Field $field, array $item): bool
    {
        return
            isset($item[$field->getField()]) &&
            is_string($item[$field->getField()]) &&
            $item[$field->getField()] === $field->getValue()
        ;
    }

    private function thenField(Field $field, array $item): array
    {
        if ($this->action instanceof OptionsInterface) {
            $this->action->setOptions([
                'key' => $field->getField(),
                'value' => $field->getValue(),
            ] + $this->context);

            return $this->action->apply($item);
        }

        return $item;
    }

    public function apply($item): array
    {
        if (true === $this->whenField($this->condition, $item)) {
            $item = $this->thenField($this->affect, $item);
        }

        return $item;
    }
}