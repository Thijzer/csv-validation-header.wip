<?php

use Misery\Component\Common\Registry\FormatRegistryInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Csv\Reader\CsvParser;
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
    ->register(new StripSlashesModifier())
    ->register(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new FormatRegistryInterface();
$formatRegistry
    ->register(new SerializeFormat())
    ->register(new FloatFormat())
    ->register(new IntFormat())
    ->register(new Misery\Component\Format\BooleanFormat())
    ->register(new Misery\Component\Format\DateTimeFormat())
    ->register(new Misery\Component\Format\ListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/private/family.yaml'));
$parser->setProcessor($processor);
$newFile->setProcessor($processor);

$compare = new Misery\Component\Csv\Combine\CsvCombine();

$csvWriter = new CsvWriter(__DIR__ . '/private/family_difference.csv');

$compare->differInto(
    new Misery\Component\Csv\Reader\CsvReader($parser),
    new Misery\Component\Csv\Reader\CsvReader($newFile),
    'code',
    function ($row) use ($csvWriter) {
        $csvWriter->write($row);
    }
);

$csvWriter = new CsvWriter(__DIR__ . '/private/family_combined.csv');

$compare->combineInto(
    new Misery\Component\Csv\Reader\CsvReader($parser),
    new Misery\Component\Csv\Reader\CsvReader($newFile),
    'code',
    function ($row) use ($csvWriter) {
        $csvWriter->write($row);
    }
);
