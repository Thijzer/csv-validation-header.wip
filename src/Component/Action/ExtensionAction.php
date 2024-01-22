<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;
use Misery\Component\Configurator\ReadOnlyConfiguration;
use Misery\Component\Extension\ExtensionInterface;

class ExtensionAction implements OptionsInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    public const NAME = 'extension';
    private null|ExtensionInterface $extension = null;

    /** @var array */
    private $options = [
        'extension' => null,
    ];

    public function apply($item): array
    {
        $extension = $this->getOption('extension');
        if (null === $extension) {
            return $item;
        }
        // loadExtension
        if (null === $this->extension) {
            $extensionFile = $this->configuration->getExtensions()[$extension.'.php'] ?? null;
            $this->extension = $this->loadExtension($extensionFile, 'Extensions\\'.$extension);
        }

        return $this->extension->apply($item);
    }

    private function loadExtension(\SplFileInfo $extension, string $extensionFile): ExtensionInterface
    {
        require_once $extension->getRealPath();

        return new $extensionFile(ReadOnlyConfiguration::loadFromConfiguration($this->configuration), $this->getOptions());
    }
}