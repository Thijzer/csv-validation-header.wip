<?php

namespace Misery\Component\Configurator;

use Misery\Component\Action\ItemActionProcessorFactory;
use Misery\Component\BluePrint\BluePrintFactory;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Pipeline\PipelineFactory;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Converter\ConverterFactory;
use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Source\ListFactory;
use Misery\Component\Source\SourceCollectionFactory;

class ConfigurationFactory
{
    private $listFactory;
    /** @var ConfigurationManager */
    private $manager;
    private $config;
    private $factoryRegistry;

    public function __construct(
        Registry $factoryRegistry
    ) {
        $this->factoryRegistry = $factoryRegistry;
    }

    public function getFactory(string $alias)
    {
        return $this->factoryRegistry->filterByAlias($alias);
    }

    public function init(LocalFileManager $manager)
    {
        $this->config = new Configuration();
        $this->manager = new ConfigurationManager(
            $this->config,
            $this,
            $this->getFactory('source')->createFromFileManager($manager),
            $manager
        );
    }

    public function parseDirectivesFromConfiguration(array $configuration): Configuration
    {
        if (isset($configuration['sources'])) {
            $this->manager->addSources($configuration['sources']);
        }

        if (isset($configuration['pipeline'])) {
            $this->manager->createPipelines($configuration['pipeline']);
        }

        if (isset($configuration['list'])) {
            $this->manager->createLists($configuration['list']);
        }

        if (isset($configuration['converter'])) {
            $this->manager->createConverter($configuration['converter']);
        }

        if (isset($configuration['blueprint'])) {
            $this->manager->createBlueprint($configuration['blueprint']);
        }

        if (isset($configuration['actions'])) {
            $this->manager->createActions($configuration['actions']);
        }

        if (isset($configuration['encoder'])) {
            $this->manager->createEncoder($configuration['encoder']);
        }

        if (isset($configuration['decoder'])) {
            $this->manager->createDecoder($configuration['decoder']);
        }

        return $this->config;
    }
}