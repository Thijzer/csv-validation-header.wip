<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\ConfigurationManager;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Writer\XmlWriter;

class PipelineFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        LocalFileManager $manager,
        ConfigurationManager $configurationManager
    ) : Pipeline {
        $pipeline = new Pipeline();

        // input is mandatory
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
            $encoder = $configurationManager->createEncoder($configuration['encoder']);
            $pipeline->line(new EncodingPipe($encoder));
        }

        if (isset($configuration['actions'])) {
            $actions = $configurationManager->createActions($configuration['actions']);
            $pipeline->line(new ActionPipe($actions));
        }

        if (isset($configuration['decoder'])) {
            $decoder = $configurationManager->createDecoder($configuration['decoder']);
            $pipeline->line(new DecodingPipe($decoder));
        }

        return $pipeline;
    }

    public function getName(): string
    {
        return 'pipeline';
    }
}