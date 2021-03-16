<?php

namespace Misery\Component\BluePrint;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\ConfigurationManager;
use Misery\Component\Source\SourceCollection;
use Symfony\Component\Yaml\Yaml;

class BluePrintFactory implements RegisteredByNameInterface
{
    private $bluePrintPath;

    public function __construct(string $bluePrintPath)
    {
        $this->bluePrintPath = $bluePrintPath;
    }

    public function createFromConfiguration(array $configuration, ConfigurationManager $configurationManager): ArrayCollection
    {
        $collection = new ArrayCollection();

        return $collection;
    }

    public function createFromName(string $name, ConfigurationManager $configurationManager)
    {
        // we check the configuration manager if we have this blueprint.

        if (false === is_file($file = $this->bluePrintPath.DIRECTORY_SEPARATOR.$name.'.yaml')) {
            // exception Config location not found
            return;
        }

        $configuration = Yaml::parseFile($file);

        ## prepare the list commands
        if (isset($configuration['list'])) {
            $configurationManager->createLists($configuration['list']);
        }

        $converter = null;
        if (isset($configuration['converter'])) {
            $converter = $configurationManager->createConverter($configuration['converter']);
        }


        return new BluePrint(
            $name,
            $configurationManager->createEncoder($configuration, $converter),
            $configurationManager->createDecoder($configuration, $converter)
        );
    }

    public function getName(): string
    {
        return 'blueprint';
    }
}