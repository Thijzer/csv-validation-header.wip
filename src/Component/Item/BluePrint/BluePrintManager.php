<?php

namespace Misery\Component\Item\BluePrint;

use Misery\Component\Action\ItemActionProcessorFactory;
use Misery\Component\Common\FileManager\FileManagerInterface;
use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Item\Handler\ItemConfigurationHandler;
use Misery\Component\Source\CreateSourcePaths;
use Misery\Component\Source\SourceCollectionFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BluePrintManager
{
    private $encoderFactory;
    private $decoderFactory;
    private $actionFactory;
    private $itemConfigurationHandler;
    private $bluePrintPath;

    public function __construct(
        ItemEncoderFactory $encoderFactory,
        ItemDecoderFactory $decoderFactory,
        ItemActionProcessorFactory $actionFactory,
        ItemConfigurationHandler $itemConfigurationHandler,
        ParameterBagInterface $parameterBag
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->decoderFactory = $decoderFactory;
        $this->actionFactory = $actionFactory;
        $this->bluePrintPath = $parameterBag->get('default_blue_print_path');
        $this->itemConfigurationHandler = $itemConfigurationHandler;
    }

    private function prepare(array $configuration, FileManagerInterface $manager)
    {
        // todo we need a blueprint manager that arranges this
        // that can create source Collections from blueprints and files
        $sourcePaths = CreateSourcePaths::create(
            $configuration['sources']['list'],
            $manager->getWorkingDirectory() . '/%s.csv',
            $this->bluePrintPath . DIRECTORY_SEPARATOR . $configuration['sources']['type'] . '/%s.yaml'
        );

        $sources = SourceCollectionFactory::create($this->encoderFactory, $this->decoderFactory, $sourcePaths);

        $handler = new ItemConfigurationHandler(
            $this->encoderFactory,
            $this->decoderFactory,
            $this->actionFactory
        );

        // TODO handler doesn't know where to write.
        // again should not be handled here
        // should be handle by the file manager
        $configuration['conversion']['output']['writer']['filename'] = $manager->getWorkingDirectory() . DIRECTORY_SEPARATOR . $configuration['conversion']['output']['writer']['filename'];

        // TODO our file handling is still done without a file manager.
        // we need this for simpler file management and possible other file systems.
        $handler->handle($configuration, $sources);
    }
}