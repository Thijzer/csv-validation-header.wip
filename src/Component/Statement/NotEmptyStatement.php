<?php

namespace Misery\Component\Statement;

use Misery\Component\Common\Options\OptionsInterface;

class NotEmptyStatement implements StatementInterface
{
    public const NAME = 'NOT_EMPTY';

    use StatementTrait;

    private function whenField(Field $field, array $item): bool
    {
        return
            isset($item[$field->getField()]) &&
            false === empty($item[$field->getField()])
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