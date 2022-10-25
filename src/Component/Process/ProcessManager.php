<?php

namespace Misery\Component\Process;

use Misery\Component\Common\Pipeline\LoggingPipe;
use Misery\Component\Configurator\Configuration;

class ProcessManager
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
    private function log(string $message)
    {
        echo $message . PHP_EOL;
    }

    public function startProcess()
    {
        $this->log(sprintf("Running Step %s ", $this->configuration->getContext('transformation_file')));

        $debug = $this->configuration->getContext('debug');
        $line = $this->configuration->getContext('line') ?? -1;
        $amount = $this->configuration->getContext('try');
        $mappings = $this->configuration->getContext('show_mappings');
        if ($line !== -1) {
            $debug = true;
            $amount = -1;
        }

        if ($pipeline = $this->configuration->getPipeline()) {
            if ($debug === true) {
                if ($mappings === true) {
                    dump($this->configuration->getMappings());
                }
                $pipeline
                    ->line(New LoggingPipe())
                    ->runInDebugMode($amount ?? 1, $line);
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