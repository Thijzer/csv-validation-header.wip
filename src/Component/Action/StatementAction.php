<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;
use Misery\Component\Common\Utils\ValueFormatter;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;
use Misery\Component\Statement\StatementBuilder;

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

        $context = [];
        if (isset($when['context']['list'])) {
            $context['list'] = $this->configuration->getList($when['context']['list']);
        }

        $statement = StatementBuilder::build($when, $context);
        if (isset($then['action']) && $then['action'] === 'skip') {
            $message = $then['skip_message'] ?? '';
            if (!empty($message)) {
                $message = ValueFormatter::format($message, $item);
            }

            throw new SkipPipeLineException($message);
        }

        if (isset($then['action']) && $then['action'] === 'skip') {
            $action = new SkipAction();
            $message = $then['skip_message'] ?? '';
            if (!empty($message)) {
                $message = ValueFormatter::format($message, $item);
            }

            $action->setOptions(['skip_message' => $message, 'force_skip' => true]);
            $statement->setAction($action);
        }

        if (isset($then['field'], $then['state'])) {
            $statement->then($then['field'], $then['state'] ?? null);
        } else {
            foreach ($then as $thenField => $thenState) {
                $statement->then($thenField, $thenState ?? null);
            }
        }

        return $statement->apply($item);
    }
}
