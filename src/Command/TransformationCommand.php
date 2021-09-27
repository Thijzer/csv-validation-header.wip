<?php

namespace Misery\Command;

use Ahc\Cli\Input\Command;
use Assert\Assertion;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Pipeline\LoggingPipe;
use Misery\Component\Common\Pipeline\Pipeline;
use Misery\Component\Process\ProcessManager;
use Symfony\Component\Yaml\Yaml;

/**
 * @usage
 * bin/console transformation --file /path/to/transformation_file --sources /path/to/sources/dir
 */
class TransformationCommand extends Command
{
    private $file;
    private $sources;
    private $debug;

    public function __construct()
    {
        parent::__construct('transformation', 'run a transform command based on a transformation file');

        $this
            ->option('-f --file', 'The transformation file location')
            ->option('-s --source', 'The sources location')
            ->option('-d --debug', 'enable debugging', 'boolval', false)
            ->usage(
                '<bold>  transformation</end> <comment>--file /path/to/transformation_file --source /path/to/sources/dir</end> ## detailed<eol/>'.
                '<bold>  transformation</end> <comment>-f /path/to/transformation -s /path/to/sources/dir</end> ## short<eol/>'
            )
        ;
    }

    public function execute(string $file, string $source, bool $debug)
    {
        $io = $this->app()->io();

        Assertion::file($file);
        Assertion::directory($source);

        require __DIR__.'/../../src/bootstrap.php';

        $fm = new LocalFileManager($source);

        $configurationFactory->init($fm);

        $configuration = $configurationFactory->parseDirectivesFromConfiguration(
            array_merge(Yaml::parseFile($file), [
                'context' => [
                    'transformation_file' => $file,
                    'sources' => $source,
                    'debug' => $debug,
                ]
            ])
        );

        (new ProcessManager($configuration))->startTransformation();
    }
}