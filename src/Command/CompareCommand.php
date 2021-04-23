<?php

namespace Misery\Command;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;
use Assert\Assert;
use Assert\Assertion;
use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Compare\ItemCompare;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;

class CompareCommand extends Command
{
    private $master;
    private $branch;
    private $reference;

    public function __construct()
    {
        parent::__construct('compare', 'Compare files');

        $this
            ->option('-m --master', 'The master file')
            ->option('-b --branch', 'The branch file')
            ->option('-r --reference', 'The references to align (comma sep)')
            ->usage(
                '<bold>  compare</end> <comment>--master /path/to/master --branch /path/to/branch --reference code</end> ## detailed<eol/>'.
                '<bold>  compare</end> <comment>-m /path/to/master -b /path/to/branch -r code</end> ## short<eol/>'
            )
        ;
    }

    public function execute(string $master, string $branch, string $reference)
    {
        $io = $this->app()->io();

        Assertion::file($master);
        Assertion::file($branch);

        $compare = new ItemCompare(
            CsvParser::create($master, ';'),
            CsvParser::create($branch, ';')
        );

        $report = $compare->compare(...array_filter(explode(',', $reference)));

        dump($report);
    }
}