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

    public function startTransformation()
    {
        $debug = $this->configuration->getContext('debug');

        /** @var Pipeline $pipeline */
        if ($pipeline = $this->configuration->getPipeline()) {

            if ($debug === true) {
                $pipeline
                    ->line(New LoggingPipe())
                    ->run(1);
                exit;
            }
            $pipeline->run();
        }
    }
}