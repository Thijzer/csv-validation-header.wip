<?php

namespace Misery\Component\Statement;

use Misery\Component\Action\ActionInterface;

trait StatementTrait
{
    /** @var ActionInterface */
    private $action;
    private $context;
    private $conditions = [];
    private $key = 0;

    private $template = [
        'or' => null,
        'and' => null,
        'when' => null,
        'then' => null,
    ];

    private function __construct() {}

    public static function prepare(ActionInterface $action, array $context = []): StatementInterface
    {
        $self = new self();
        $self->action = $action;
        $self->context = $context;

        return $self;
    }

    public function when(string $field, string $value = null): StatementInterface
    {
        $this->key++;

        $this->conditions[$this->key] = ['when' => new Field($field, $value)];

        return $this;
    }

    public function or(string $field, string $value = null): StatementInterface
    {
        $this->conditions[$this->key] = $this->conditions[$this->key]+['or' => new Field($field, $value)];

        return $this;
    }

    public function and(string $field, string $value = null): StatementInterface
    {
        $this->conditions[$this->key] = $this->conditions[$this->key]+['and' => new Field($field, $value)];

        return $this;
    }

    public function then(string $field, string $value = null): void
    {
        if (isset($this->conditions[$this->key])) {
            $this->conditions[$this->key] = $this->conditions[$this->key]+['then' => new Field($field, $value)];
        }
    }

    public function isApplicable(): bool
    {
        // TODO
    }

    public function apply(array $item): array
    {
        foreach ($this->conditions as $condition) {
            $condition = array_merge($this->template, $condition);
            switch (true) {
                case !empty($condition['or']) && (true === $this->whenField($condition['when'], $item) || true === $this->whenField($condition['or'], $item)):
                case !empty($condition['and']) && (true === $this->whenField($condition['when'], $item) && true ===  $this->whenField($condition['and'], $item)):
                case true === $this->whenField($condition['when'], $item):
                    $item = $this->thenField($condition['then'], $item);
                    break;
                default:
                    break;
            }
        }

        return $item;
    }
}