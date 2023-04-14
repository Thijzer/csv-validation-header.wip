<?php

namespace Misery\Component\Statement;

class CharlenStatement implements PredeterminedStatementInterface
{
    public const NAME = 'CHAR_LEN';

    use StatementTrait;

    private function whenField(Field $field, array $item): bool
    {
        switch ($this->context['condition']) {
            case 'greater_than':
                return isset($item[$field->getField()]) && (strlen($item[$field->getField()]) > $this->context['char_len']);
                break;
        }

        throw new \Exception('Unknown statement condition set.');
    }
}