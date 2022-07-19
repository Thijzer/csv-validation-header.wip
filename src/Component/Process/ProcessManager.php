<?php

namespace Misery\Component\Process;

use Misery\Component\Common\Pipeline\LoggingPipe;
use Misery\Component\Common\Pipeline\Pipeline;
use Misery\Component\Configurator\Configuration;

class ProcessManager
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function startProcess()
    {
        $debug = $this->configuration->getContext('debug');
        $amount = $this->configuration->getContext('try');
        $mappings = $this->configuration->getContext('show_mappings');

        if ($pipeline = $this->configuration->getPipeline()) {
            if ($debug === true) {
                if ($mappings === true) {
                    dump($this->configuration->getMappings());
                }
                $pipeline
                    ->line(New LoggingPipe())
                    ->run($amount ?? 1);
                exit;
            }

            if (is_int($amount)) {
                $pipeline->run($amount);
                exit;
            }

            $pipeline->run();
        }
    }
}