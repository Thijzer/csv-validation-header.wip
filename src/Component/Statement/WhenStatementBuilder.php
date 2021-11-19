<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\SetValueAction;

class WhenStatementBuilder
{
    public static function buildFromOperator(string $operator, array $context = []): StatementInterface
    {
        switch ($operator) {
            case 'IN_LIST':
                $statement = InListStatement::prepare(new SetValueAction(), $context);
                break;
            case 'EQUALS':
            case null:
                $statement = EqualsStatement::prepare(new SetValueAction());
                break;
            case 'CONTAINS':
                $statement = ContainsStatement::prepare(new SetValueAction());
                break;
            case 'EMPTY':
                $statement = EmptyStatement::prepare(new SetValueAction());
                break;
            case 'NOT_EMPTY':
                $statement = NotEmptyStatement::prepare(new SetValueAction());
                break;
            default:
                throw new \Exception('invalid statement operator');
        }

        return $statement;
    }

    public static function build($when, array $then, StatementInterface $statement): void
    {
        if (is_string($when)) {
            static::fromExpression($when, $statement);
        } else {
            static::fromArray($when, $statement);
        }

        if (isset($then['field'], $then['state'])) {
            $statement->then($then['field'], $then['state'] ?? null);
            return;
        }
        foreach ($then as $thenField => $thenState) {
            $statement->then($thenField, $thenState ?? null);
        }
    }

    public static function fromExpression(string $whenString, StatementInterface $statement): void
    {
        $andFields = explode(' AND ', $whenString) ?? [];
        if (count($andFields) === 2) {
            $fields = explode(' == ', $andFields[0]);
            $statement->when($fields[0], $fields[1]);
            $fields = explode(' == ', $andFields[1]);
            $statement->and($fields[0], $fields[1]);
        }

        $orFields = explode(' OR ', $whenString) ?? [];
        if (count($orFields) === 2) {
            $fields = explode(' == ', $orFields[0]);
            $statement->when($fields[0], $fields[1]);
            $fields = explode(' == ', $orFields[1]);
            $statement->or($fields[0], $fields[1]);
        }
    }

    public static function fromArray(array $whenArray, StatementInterface $statement): void
    {
        $statement->when($whenArray['field'], $whenArray['state'] ?? null);
    }
}