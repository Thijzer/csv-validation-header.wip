<?php

namespace Misery\Command;

use Ahc\Cli\Input\Command;
use Assert\Assertion;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Process\ProcessManager;
use Symfony\Component\Yaml\Yaml;

/**
 * @usage
 * bin/console transformation --file /path/to/transformation_file --sources /path/to/sources/dir --workpath /path/to/work-dir
 */
class TransformationCommand extends Command
{
    private $file;
    private $sources;
    private $debug;
    private $showMappings;
    private $try;
    private $workpath;

    public function __construct()
    {
        parent::__construct('transformation', 'run a transform command based on a transformation file');

        $this
            ->option('-f --file', 'The transformation file location')
            ->option('-s --source', 'The sources location')
            ->option('-d --debug', 'enable debugging', 'boolval', false)
            ->option('-m --showMappings', 'show lists or mappings', 'boolval', false)
            ->option('-t --try', 'tryout a set for larger files')
            ->option('-l --line', 'target a line nr')
            ->option('-w --workpath', 'target work path')

            ->usage(
                '<bold>  transformation</end> <comment>--file /path/to/transformation_file --source /path/to/sources/dir</end> ## detailed<eol/>'.
                '<bold>  transformation</end> <comment>-f /path/to/transformation -s /path/to/sources/dir</end> ## short<eol/>'
            )
        ;
    }

    public function execute(string $file, string $source, string $workpath, bool $debug, int $line = null, int $try = null, bool $showMappings = null)
    {
        $io = $this->app()->io();

        Assertion::file($file);
        Assertion::directory($source);
        Assertion::directory($workpath);

        require_once __DIR__.'/../../src/bootstrap.php';

        $configurationFactory = initConfigurationFactory();
        $configurationFactory->init(
            new LocalFileManager($source),
            new LocalFileManager($workpath)
        );

        $configuration = $configurationFactory->parseDirectivesFromConfiguration(
            array_merge(Yaml::parseFile($file), [
                'context' => [
                    'transformation_file' => $file,
                    'sources' => $source,
                    'scripts' => __DIR__.'/../../scripts',
                    'workpath' => $workpath,
                    'debug' => $debug,
                    'try' => $try,
                    'line' => $line,
                    'show_mappings' => $showMappings,
                ]
            ])
        );

        if (false === $configuration->isMultiStep()) {
            (new ProcessManager($configuration))->startProcess();

            // TODO connect the outputs here
            if ($shellCommands = $configuration->getShellCommands()) {
                $shellCommands->exec();
                $configuration->clearShellCommands();
            }
        }
    }
}