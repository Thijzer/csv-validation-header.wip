<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Source\Command\ExecuteSourceCommandInterface;

class ListFactory implements RegisteredByNameInterface
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry)
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createFromConfiguration(array $configuration, SourceCollection $collection): array
    {
        $commands = [];
        foreach ($configuration as $listCommand) {
            if ($class = $this->getCommandClass($listCommand['source_command'])) {

                if ($class instanceof OptionsInterface && isset($listCommand['options'])) {
                    $options = $listCommand['options'];
                    $class->setOptions($options);
                }

                if ($class instanceof SourceAwareInterface && isset($listCommand['source'])) {
                    $class->setSource($collection->get($listCommand['source']));
                }

                // this might become a late call
                $commands[$listCommand['name']] = $class->execute();
            }
        }

        return $commands;
    }

    private function getCommandClass(string $alias): ExecuteSourceCommandInterface
    {
        return $this->registryCollection['source_command']->filterByAlias($alias);
    }

    public function getName(): string
    {
        return 'list';
    }
}