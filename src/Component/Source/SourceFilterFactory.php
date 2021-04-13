<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Source\Command\ExecuteSourceCommandInterface;

class SourceFilterFactory implements RegisteredByNameInterface
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createFromConfiguration(array $configuration, SourceCollection $collection)
    {
        // we have to apply the filters like an encoder, so we can execute on demand
        $filters = [];
        foreach ($configuration as $listCommand) {
            $filters[$listCommand['name']] = new SourceFilter(
                $collection->get($listCommand['source']),
                $class = $this->getCommandClass('filter'),
                $listCommand['filter']
            );

            if ($class instanceof OptionsInterface && isset($listCommand['options'])) {
                $options = $listCommand['options'];
                $class->setOptions($options);
            }

            if ($class instanceof SourceAwareInterface && isset($listCommand['source'])) {
                $class->setSource($collection->get($listCommand['source']));
            }
        }

        return $filters;
    }

    private function getCommandClass(string $alias): ExecuteSourceCommandInterface
    {
        $filter = $this->registryCollection['source_command']->filterByAlias($alias);

        return clone $filter;
    }

    public function getName(): string
    {
        return 'filter';
    }
}