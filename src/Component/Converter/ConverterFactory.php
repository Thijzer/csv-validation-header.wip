<?php

namespace Misery\Component\Converter;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Common\Registry\RegistryInterface;
use Misery\Component\Configurator\Configuration;
use Misery\Component\Configurator\ConfigurationAwareInterface;

class ConverterFactory implements RegisteredByNameInterface
{
    private $registryCollection;

    public function addRegistry(RegistryInterface $registry): ConverterFactory
    {
        $this->registryCollection[$registry->getAlias()] = $registry;

        return $this;
    }

    public function createFromConfiguration(array $configuration, Configuration $config): ConverterInterface
    {
        /** @var ConverterInterface $converter */
        $converter = clone $this->registryCollection['converter']->filterByAlias($configuration['name']);

        if ($converter instanceof OptionsInterface && isset($configuration['options'])) {
            $options = $configuration['options'];
            $converter->setOptions($options);
        }

        if ($converter instanceof ConfigurationAwareInterface) {
            $converter->setConfiguration($config);
        }

        if ($converter instanceof OptionsInterface && isset($configuration['options'])) {
            $options = $configuration['options'];
            // fetch those values
            foreach ($options as $option => $optionValue) {
                if (strpos($option, ':list') !== false) {
                    $converter->setOptions([$option => $config->getList($optionValue)]);
                }
            }

            if ($list = $converter->getOption('list')) {
                $converter->setOptions(['list' => $config->getList($list)]);
            }
        }

        return $converter;
    }

    public function getName(): string
    {
        return 'converter';
    }
}