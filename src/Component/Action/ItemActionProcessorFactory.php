<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Configurator\ConfigurationManager;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Source\SourceCollection;

class ItemActionProcessorFactory implements RegisteredByNameInterface
{
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createActionProcessor(SourceCollection $sources, array $configuration): ItemActionProcessor
    {
        return new ItemActionProcessor(
            $this->prepRulesFromConfiguration($sources, $configuration)
        );
    }

    public function createFromConfiguration(array $configuration, ConfigurationManager $manager, SourceCollection $sources): ItemActionProcessor
    {
        return new ItemActionProcessor(
            $this->prepRulesFromConfiguration($sources, $configuration, $manager)
        );
    }

    private function prepRulesFromConfiguration(SourceCollection $sources, array $configuration, ConfigurationManager $manager = null): array
    {
        $rules = [];
        foreach ($configuration as $name => $value) {
            $action = $value['action'] ?? null;
            unset($value['action']);

            if ($action = $this->registry->filterByAlias($action)) {

                $action = clone $action;

                if ($manager && isset($value['filter']) && is_string($value['filter'])) {
                    $value['filter'] = $manager->getConfig()->getFilter($value['filter']);
                }

                if ($action instanceof OptionsInterface && !empty($value)) {
                    if ($manager && isset($value['list']) && is_string($value['list'])) {
                        $value['list'] = $manager->getConfig()->getList($value['list']);
                    }
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

    public function getName(): string
    {
        return 'action';
    }
}