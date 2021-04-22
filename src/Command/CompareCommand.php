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
            ->option('-r --reference', 'The references (comma sep)')
            ->usage(
                '<bold>  compare</end> <comment>--master /path/to/master --branch /path/to/branch --reference code</end> ## details<eol/>'.
                '<bold>  compare</end> <comment>-m /path/to/master -b /path/to/branch -r code</end> ## short<eol/>'

            )
        ;
    }

//    public function interact(Interactor $io)
//    {
//        if (!$this->master) {
//            $this->set('master', $io->prompt('Enter your master file'));
//        }
//
//        if (!$this->branch) {
//            $this->set('branch', $io->prompt('Enter your branched file'));
//        }
//
//        if (!$this->reference) {
//            $this->set('reference', $io->prompt('Enter your references'));
//        }
//    }

    public function execute(string $master, string $branch, $reference)
    {
        $io = $this->app()->io();

        Assertion::file($master);
        Assertion::file($branch);

        $compare = new ItemCompare(
            CsvParser::create($master, ';'),
            CsvParser::create($branch, ';')
        );

        $dump = $compare->compare(...explode(',', $reference));

        dump($dump);
    }
}