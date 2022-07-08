<?php

namespace Misery\Component\Statement;

use Misery\Component\Common\Options\OptionsInterface;

class EmptyStatement implements StatementInterface
{
    public const NAME = 'EMPTY';

    use StatementTrait;

    private function whenField(Field $field, array $item): bool
    {
        return
            isset($item[$field->getField()]) &&
            empty($item[$field->getField()])
        ;
    }
}