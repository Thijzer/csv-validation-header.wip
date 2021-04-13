<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\ConfigurationManager;

class PipelineFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        ConfigurationManager $configurationManager
    ) : Pipeline {
        $pipeline = new Pipeline();

        // input is mandatory
        $reader = $configurationManager->createReader($configuration['input']['reader']);
        $pipeline->input(new PipeReader($reader));

        foreach ($configuration as $key => $valueConfiguration) {
            switch (true) {
                case isset($configuration[$key]['writer']);
                    $writer = $configurationManager->createWriter($configuration['output']['writer']);
                    $pipeline->output(new PipeWriter($writer));
                    break;
                case $key === 'encoder';
                    $encoder = $configurationManager->createEncoder($configuration['encoder']);
                    $pipeline->line(new EncodingPipe($encoder));
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

    public function getName(): string
    {
        return 'pipeline';
    }
}