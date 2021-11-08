<?php

namespace Misery\Component\Statement;

use Misery\Component\Common\Options\OptionsInterface;

class InListStatement implements StatementInterface
{
    public const NAME = 'IN_LIST';

    use StatementTrait;

    private function whenField(Field $field, array $item): bool
    {
        return
            isset($item[$field->getField()]) &&
            is_string($item[$field->getField()]) &&
            isset($this->context['list']) &&
            is_array($this->context['list']) &&
            in_array($item[$field->getField()], $this->context['list'])
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
}