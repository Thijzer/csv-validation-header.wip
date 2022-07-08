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
}