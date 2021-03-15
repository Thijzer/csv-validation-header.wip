<?php

namespace Misery\Component\Statement;

use Misery\Component\Common\Registry\RegistryInterface;

class StatementFactory
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createStatements(array $configuration)
    {
        return new StatementCollection($this->prepRulesFromConfiguration($configuration));
    }

    private function prepRulesFromConfiguration(array $configuration): array
    {
        $rules = [];
        foreach ($configuration ?? [] as $entry) {
            $operator = $entry['when']['operator'] ?? null;
            $action = $entry['then']['action'] ?? null;
            $context = $entry['context'] ?? [];
            $statement = $this->getNamedStatement($operator);
            $action = $this->getNamedAction($action);

            if ($statement && $action) {
                /** @var StatementInterface $statement */
                $statement = $statement::prepare($action, $context);
                $statement
                    ->when($entry['when']['field'], $entry['when']['state'])
                    ->then($entry['then']['field'], $entry['then']['state'])
                ;
                $rules[] = $statement;
            }
        }

        return $rules;
    }

    private function getNamedAction(string $name)
    {
        return $this->registryCollection['action']->filterByAlias($name) ?? null;
    }

    private function getNamedStatement(string $name)
    {
        return $this->registryCollection['statement']->filterByAlias($name) ?? null;
    }
}