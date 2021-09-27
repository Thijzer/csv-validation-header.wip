<?php

namespace Misery\Component\Configurator;

use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\Registry;

class ConfigurationFactory
{
    /** @var ConfigurationManager */
    private $manager;
    private $config;
    private $factoryRegistry;

    public function __construct(Registry $factoryRegistry)
    {
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
        // level 0 directives only
        foreach ($configuration as $key => $valueConfiguration) {
            switch ($key) {
                case $key === 'context';
                    $this->manager->addContext($configuration['context']);
                    break;
                case $key === 'sources';
                    $this->manager->addSources($configuration['sources']);
                    break;
                case $key === 'transformation_steps';
                    $this->manager->addTransformationSteps($configuration['transformation_steps']);
                    break;
                case $key === 'pipeline';
                    $this->manager->createPipelines($configuration['pipeline']);
                    break;
                case $key === 'list';
                    $this->manager->createLists($configuration['list']);
                    break;
                case $key === 'filter';
                    $this->manager->createFilters($configuration['filter']);
                    break;
                case $key === 'converter';
                    $this->manager->createConverter($configuration['converter']);
                    break;
                case $key === 'blueprint';
                    $this->manager->createBlueprints($configuration['blueprint']);
                    break;
                case $key === 'actions';
                    $this->manager->createActions($configuration['actions']);
                    break;
                case $key === 'encoder';
                    $this->manager->createEncoder($configuration['encoder']);
                    break;
                case $key === 'decoder';
                    $this->manager->createDecoder($configuration['decoder']);
                    break;
            }
        }

        return $this->config;
    }
}