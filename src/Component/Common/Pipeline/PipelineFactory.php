<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\ConfigurationManager;
use Misery\Component\Writer\ItemWriterInterface;

class PipelineFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        ConfigurationManager $configurationManager
    ): Pipeline {
        $pipeline = new Pipeline();

        // input is mandatory
        $reader = $configurationManager->createReader($configuration['input']['reader']);
        $pipeline->input(new PipeReader($reader));

        foreach ($configuration as $key => $valueConfiguration) {
            switch (true) {
                case $key === 'output' && isset($configuration['output']['http']):
                    $writer = $configurationManager->createHTTPWriter($configuration['output']['http']);
                    $pipeline->output(new PipeWriter($writer));

                    $pipeline->invalid(
                        new PipeWriter($this->createInvalid($configurationManager, $configuration))
                    );
                    break;

                case isset($configuration[$key]['writer']);
                    $writer = $configurationManager->createWriter($configuration['output']['writer']);
                    $pipeline->output(new PipeWriter($writer));

                    $pipeline->invalid(
                        new PipeWriter($this->createInvalid($configurationManager, $configuration))
                    );
                    break;

                case $key === 'encoder';
                    $encoder = $configurationManager->createEncoder($configuration['encoder']);
                    $pipeline->line(new EncodingPipe($encoder));
                    break;

                case $key === 'blueprint';
                    $blueprint = $configurationManager->createBlueprint($configuration['blueprint']);
                    $pipeline->line(new EncodingPipe($blueprint->getEncoder()));
                    if ($blueprint->getConverter()) {
                        $pipeline->line(new ConverterPipe($blueprint->getConverter()));
                        $pipeline->line(new RevertPipe($blueprint->getConverter()));
                    }
                    $pipeline->line(new DecodingPipe($blueprint->getDecoder()));
                    // what about decode and revert
                    break;
                case $key === 'actions';
                    $actions = $configurationManager->createActions($configuration['actions']);
                    $pipeline->line(new ActionPipe($actions));
                    break;
                case $key === 'decoder';
                    $decoder = $configurationManager->createDecoder($configuration['decoder']);
                    $pipeline->line(new DecodingPipe($decoder));
                    break;
            }
        }

        return $pipeline;
    }

    private function createInvalid(ConfigurationManager $configurationManager, array $configuration): ItemWriterInterface
    {
        $configuration['output']['writer']['filename'] = 'invalid_items.csv';
        $configuration['output']['writer']['type'] = 'csv';
        $configuration['output']['writer']['format']['mode'] = 'append';

        return $configurationManager->createWriter($configuration['output']['writer']);
    }

    public function getName(): string
    {
        return 'pipeline';
    }
}