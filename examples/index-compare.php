<?php

use Misery\Component\Common\Registry\FormatRegistry;
use Misery\Component\Common\Registry\ModifierRegistry;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;

require __DIR__.'/../vendor/autoload.php';

$parser = CsvParser::create(__DIR__ . '/tests/examples/family.csv', ';');
$newFile = CsvParser::create(__DIR__ . '/tests/examples/family_new.csv', ';');

$modifierRegistry = new ModifierRegistry();
$modifierRegistry->register(new StripSlashesModifier());
$modifierRegistry->register(new Misery\Component\Modifier\ArrayUnflattenModifier());
$modifierRegistry->register(new Misery\Component\Modifier\NullifyEmptyStringModifier());


$formatRegistry = new FormatRegistry();
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

$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/tests/examples/family.yaml'));

$reader = new Misery\Component\Csv\Reader\CsvReader($parser, $processor);

$secondReader = new Misery\Component\Csv\Reader\CsvReader($newFile, $processor);

$compare = new Misery\Component\Csv\Compare\CsvCompare(
    $reader,
    $secondReader
);

dump(
    $compare->compare('code')
);