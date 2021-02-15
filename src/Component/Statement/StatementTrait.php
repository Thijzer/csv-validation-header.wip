<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\ActionInterface;
use Misery\Component\Common\Options\OptionsInterface;

trait StatementTrait
{
    /** @var ActionInterface */
    private $action;
    private $context;
    private $conditions;
    private $key = 0;

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
        $this->key++;

        $this->conditions[$this->key] = ['when' => new Field($field, $value)];

        return $this;
    }

    public function then(string $field, string $value): void
    {
        if (isset($this->conditions[$this->key])) {
            $this->conditions[$this->key] = $this->conditions[$this->key]+['then' => new Field($field, $value)];
        }
    }

    public function apply($item): array
    {
        foreach ($this->conditions as $condition) {
            if (true === $this->whenField($condition['when'], $item)) {
                $item = $this->thenField($condition['then'], $item);
            }
        }

        return $item;
    }
}