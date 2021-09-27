<?php

namespace Misery\Component\Statement;

class WhenStatementBuilder
{
    public static function build($when, array $then, StatementInterface $statement): void
    {
        if (is_string($when)) {
            static::fromExpression($when, $statement);
        } else {
            static::fromArray($when, $statement);
        }

        $statement->then($then['field'], $then['state'] ?? null);
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