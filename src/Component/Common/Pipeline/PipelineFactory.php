<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Action\ItemActionProcessorFactory;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Source\SourceCollection;
use Misery\Component\Writer\XmlWriter;

class PipelineFactory
{
    /** @var ItemEncoderFactory */
    private $encoderFactory;
    /** @var ItemDecoderFactory */
    private $decoderFactory;
    /** @var ItemActionProcessorFactory */
    private $actionFactory;

    public function __construct(
        ItemEncoderFactory $encoderFactory,
        ItemDecoderFactory $decoderFactory,
        ItemActionProcessorFactory $actionFactory
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->decoderFactory = $decoderFactory;
        $this->actionFactory = $actionFactory;
    }

    public function createFromConfiguration(array $configuration, LocalFileManager $manager) : Pipeline
    {
        $context = $configuration['context'];
        $configuration = $configuration['pipeline'];

        $pipeline = new Pipeline();
        $sources = new SourceCollection('akeneo/csv');

        $reader = new ItemReader(CsvParser::create(
            $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['input']['reader']['filename'],
            $configuration['input']['reader']['delimiter'] ?? CsvParser::DELIMITER,
            $configuration['input']['reader']['enclosure'] ?? CsvParser::ENCLOSURE
        ));
        $pipeline->input(new PipeReader($reader));

        if (isset($configuration['output'])) {
            $writer = new XmlWriter(
                $manager->getWorkingDirectory() . DIRECTORY_SEPARATOR . $configuration['output']['writer']['filename'],
                $configuration['output']['writer']['options'] ?? []
            );
            $pipeline->output(new PipeWriter($writer));
        }
        if (isset($configuration['encoder'])) {
            $encoder = $this->encoderFactory->createItemEncoder($configuration['encoder']);
            $pipeline->line(new EncodingPipe($encoder));
        }
        if (isset($configuration['actions'])) {
            $actions = $this->actionFactory->createActionProcessor(
                $sources,
                $configuration['actions']
            );
            $pipeline->line(new ActionPipe($actions));
        }
        if (isset($configuration['decoder'])) {
            $decoder = $this->decoderFactory->createItemDecoder($configuration['decoder']);
            $pipeline->line(new DecodingPipe($decoder));
        }

        return $pipeline;
    }
}