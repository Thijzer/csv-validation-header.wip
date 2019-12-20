<?php

use Misery\Component\Common\Registry\Registry;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;
use Misery\Component\Format\BooleanFormat;
use Misery\Component\Format\DateTimeFormat;
use Misery\Component\Format\ListFormat;
use Misery\Component\Modifier\ArrayUnflattenModifier;
use Misery\Component\Modifier\NullifyEmptyStringModifier;

require __DIR__.'/../vendor/autoload.php';

$parser = CsvParser::create(__DIR__ . '/private/family.csv', ';');
$newFile = CsvParser::create(__DIR__ . '/private/family_new.csv', ';');

$modifierRegistry = new Registry();
$modifierRegistry
    ->register(StripSlashesModifier::NAME, new StripSlashesModifier())
    ->register(ArrayUnflattenModifier::NAME, new ArrayUnflattenModifier())
    ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
;

$formatRegistry = new Registry();
$formatRegistry
    ->register(SerializeFormat::NAME, new SerializeFormat())
    ->register(FloatFormat::NAME, new FloatFormat())
    ->register(IntFormat::NAME, new IntFormat())
    ->register(BooleanFormat::NAME, new BooleanFormat())
    ->register(DateTimeFormat::NAME, new DateTimeFormat())
    ->register(ListFormat::NAME, new ListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/private/family.yaml'));
$parser->setProcessor($processor);
$newFile->setProcessor($processor);

$compare = new Misery\Component\Csv\Compare\CsvCompare(
   new Misery\Component\Csv\Reader\RowReader($parser),
   new Misery\Component\Csv\Reader\RowReader($newFile)
);

dump(
    $compare->compare('code')
);