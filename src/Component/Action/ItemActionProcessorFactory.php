<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Source\SourceCollection;

class ItemActionProcessorFactory
{
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createActionProcessor(SourceCollection $sources, array $configuration)
    {
        return new ItemActionProcessor(
            $this->prepRulesFromConfiguration($sources, $configuration)
        );
    }

    private function prepRulesFromConfiguration(SourceCollection $sources, array $configuration): array
    {
        $rules = [];
        foreach ($configuration as $name => $value) {
            $action = $value['action'] ?? null;
            unset($value['action']);

            if ($action = $this->registry->filterByAlias($action)) {

                $action = clone $action;

                if ($action instanceof OptionsInterface && !empty($value)) {
                    $action->setOptions($value);
                }

                if ($action instanceof ItemReaderAwareInterface && isset($value['source'])) {
                    $action->setReader($sources->get($value['source'])->getReader());
                }

                $rules[$name] = $action;
            }
        }

        return $rules;
    }
}