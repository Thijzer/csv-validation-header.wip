<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\SetValueAction;

class StatementBuilder
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
            case 'NOT_EQUAL':
                $statement = NotEqualStatement::prepare(new SetValueAction());
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

    public static function build($when, array $context = []): StatementInterface
    {
        $statement = null;

        if (is_string($when)) {
            $statement = static::fromExpression($when);
        }
        if (is_array($when)) {
            $operator = $when['operator'] ?? null;
            $context = array_merge($when['context'] ?? [], $context);

            if ($operator) {
                $statement = self::buildFromOperator($operator, $context);
                $statement->when($when['field'], $when['state'] ?? null);
            }
        }

        if (null === $statement) {
            throw new \RuntimeException('Unprocessable WHEN field');
        }

        return $statement;
    }

    private static function fromExpression(string $whenString): StatementInterface
    {
        $statement = EqualsStatement::prepare(new SetValueAction());

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

        $containsFields = explode(' CONTAINS ', $whenString) ?? [];
        if (count($containsFields) === 2) {
            $statement = ContainsStatement::prepare(new SetValueAction());
            $statement->when($containsFields[0], $containsFields[1]);
        }

        return $statement;
    }

    public static function fromArray(array $whenArray, array $context = []): array
    {
        $statements = [];
        foreach ($whenArray as $when) {
            $statements[] = self::build($when);
        }

        return $statements;
    }
}