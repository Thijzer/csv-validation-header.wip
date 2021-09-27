<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Statement\EqualsStatement;
use Misery\Component\Statement\WhenStatementBuilder;

class StatementAction implements OptionsInterface
{
    use OptionsTrait;

    private $statement;

    public const NAME = 'statement';

    /** @var array */
    private $options = [
        'when' => [],
        'then' => [],
    ];

    public function apply(array $item): array
    {
        $this->statement = EqualsStatement::prepare(new SetValueAction());

        $when = $this->getOption('when');
        $then = $this->getOption('then');

        WhenStatementBuilder::build($when, $then, $this->statement);

        return $this->statement->apply($item);
    }
}