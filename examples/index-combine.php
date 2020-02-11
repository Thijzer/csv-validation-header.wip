<?php

use Misery\Component\Common\Registry\FormatRegistryInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Csv\Writer\CsvWriter;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;

require __DIR__.'/../vendor/autoload.php';

$parser = CsvParser::create(__DIR__ . '/private/family.csv', ';');
$newFile = CsvParser::create(__DIR__ . '/private/family_new.csv', ';');

$modifierRegistry = new Registry();
$modifierRegistry
    ->registerNamedObject(new StripSlashesModifier())
    ->registerNamedObject(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->registerNamedObject(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new Registry();
$formatRegistry
    ->registerNamedObject(new SerializeFormat())
    ->registerNamedObject(new FloatFormat())
    ->registerNamedObject(new IntFormat())
    ->registerNamedObject(new Misery\Component\Format\BooleanFormat())
    ->registerNamedObject(new Misery\Component\Format\DateTimeFormat())
    ->registerNamedObject(new Misery\Component\Format\ListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/private/family.yaml'));
$parser->setProcessor($processor);
$newFile->setProcessor($processor);

$compare = new \Misery\Component\Combine\ItemCombine();

$csvWriter = new CsvWriter(__DIR__ . '/private/family_difference.csv');

$compare->differInto(
    new Misery\Component\Reader\ItemReader($parser),
    new Misery\Component\Reader\ItemReader($newFile),
    'code',
    function ($row) use ($csvWriter) {
        $csvWriter->write($row);
    }
);

$csvWriter = new CsvWriter(__DIR__ . '/private/family_combined.csv');

$compare->combineInto(
    new Misery\Component\Reader\ItemReader($parser),
    new Misery\Component\Reader\ItemReader($newFile),
    'code',
    function ($row) use ($csvWriter) {
        $csvWriter->write($row);
    }
);
