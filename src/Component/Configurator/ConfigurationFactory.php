<?php

namespace Misery\Component\Configurator;

use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Common\Utils\ValueFormatter;

class ConfigurationFactory
{
    /** @var ConfigurationManager */
    private $manager;
    /** @var Configuration */
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

    public function init(LocalFileManager $source, LocalFileManager $workpath)
    {
        $this->config = new Configuration();
        $this->manager = new ConfigurationManager(
            $this->config,
            $this,
            $this->getFactory('source')->createFromFileManager($source),
            $source,
            $workpath
        );
    }

    public function parseDirectivesFromConfiguration(array $configuration): Configuration
    {
        // sort the keys
        $order = [
            'context',
            'sources',
            'list',
            'mapping',
            'blueprint',
        ];

        // remove unused keys
        $order = array_filter($order, function ($orderItem) use ($configuration) {
            return key_exists($orderItem, $configuration);
        });
        $configuration = array_merge(array_flip($order), $configuration);

        // level 0 directives only
        foreach ($configuration as $key => $valueConfiguration) {
            switch ($key) {
                case $key === 'context';
                    $this->manager->addContext($configuration['context']);
                    break;
                case $key === 'sources';
                    // ValueFormatter converts %workpath% or other context params
                    $this->manager->addSources(
                        ValueFormatter::formatMulti($configuration['sources'], $this->config->getContext())
                    );
                    break;
                case $key === 'account';
                    $this->manager->createAccounts($configuration['account']);
                    break;
                case $key === 'transformation_steps';
                    $this->config->setAsMultiStep();
                    // @todo move this to a dedicated logger
                    echo sprintf("Multi Step [%s]", $this->config->getContext('transformation_file')). PHP_EOL;
                    $this->manager->addTransformationSteps($configuration['transformation_steps']);
                    break;
                case $key === 'pipeline';
                    $this->manager->createPipelines($configuration['pipeline']);
                    break;
                case $key === 'shell';
                    $this->manager->createShellCommands($configuration['shell']);
                    break;
                case $key === 'list';
                    $this->manager->createLists($configuration['list']);
                    break;
                case $key === 'mapping';
                    $this->manager->createMapping($configuration['mapping']);
                    break;
                case $key === 'filter';
                    $this->manager->createFilters($configuration['filter']);
                    break;
                case $key === 'converter';
                    $this->manager->createConverter($configuration['converter']);
                    break;
                case $key === 'feed';
                    $this->manager->createFeed($configuration['feed']);
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