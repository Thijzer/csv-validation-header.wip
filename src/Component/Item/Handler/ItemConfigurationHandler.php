<?php

namespace Misery\Component\Item\Handler;

use Misery\Component\Action\ItemActionProcessorFactory;
use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Source\CreateSourcePaths;
use Misery\Component\Source\SourceCollection;
use Misery\Component\Source\SourceCollectionFactory;
use Misery\Component\Writer\CsvWriter;
use Symfony\Component\Yaml\Yaml;

class ItemConfigurationHandler
{
    /**
     * @var ItemEncoderFactory
     */
    private $encoderFactory;
    /**
     * @var ItemDecoderFactory
     */
    private $decoderFactory;
    /**
     * @var ItemActionProcessorFactory
     */
    private $actionFactory;

    public function __construct(ItemEncoderFactory $encoderFactory, ItemDecoderFactory $decoderFactory, ItemActionProcessorFactory $actionFactory)
    {
        $this->encoderFactory = $encoderFactory;
        $this->decoderFactory = $decoderFactory;
        $this->actionFactory = $actionFactory;
    }

    public function handle($configuration): void
    {
        try {
            if (false === is_array($configuration) && is_file($configuration)) {
                $configuration = Yaml::parseFile($configuration);
            }

            // TODO validate configuration here

            $sources = SourceCollectionFactory::create($this->encoderFactory, $this->decoderFactory, $configuration['sources']);

            // blend client configuration and customer configuration
            $actionProcessor = $this->actionFactory->createActionProcessor($sources, $configuration['conversion']['actions'] ?? []);

            $source = $sources->get($configuration['conversion']['data']['source']);

            $writer = CsvWriter::createFromArray($configuration['conversion']['output']['writer']);
            $writer->clear();

            // decoder needs to happen before write
            foreach ($source->getReader()->getIterator() as $item) {
                $writer->write($source->decode($actionProcessor->process($item)));
            }

        } catch (\Exception $e) {
            throw new \Exception(
                sprintf(
                    'Invalid Configuration File : %s : %s : %s : %s',
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile(),
                    \json_encode($configuration)
                ), $e->getCode(), $e);
        }
    }
}