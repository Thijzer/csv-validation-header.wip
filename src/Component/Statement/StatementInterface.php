<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\ActionInterface;

interface StatementInterface
{
    public static function prepare(ActionInterface $action, array $context = []);
    public function apply(array $item): array;
    public function when(string $field, string $value): self;
    public function then(string $field, string $value): void;
}