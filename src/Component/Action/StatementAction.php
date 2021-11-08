<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;
use Misery\Component\Statement\ContainsStatement;
use Misery\Component\Statement\EmptyStatement;
use Misery\Component\Statement\EqualsStatement;
use Misery\Component\Statement\InListStatement;
use Misery\Component\Statement\NotEmptyStatement;
use Misery\Component\Statement\WhenStatementBuilder;

class StatementAction implements OptionsInterface, ConfigurationAwareInterface
{
    use ConfigurationTrait;
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
        $when = $this->getOption('when');
        $then = $this->getOption('then');

        $operator = $when['operator'] ?? null;
        $context = $when['context'] ?? null;

        if (isset($context['list'])) {
            $context['list'] = $this->configuration->getList($context['list']);
        }

        $this->statement = WhenStatementBuilder::buildFromOperator($operator, $context);

        WhenStatementBuilder::build($when, $then, $this->statement);

        return $this->statement->apply($item);
    }
}