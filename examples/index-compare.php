<?php

use Misery\Component\Common\Registry\Registry;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Format\StringToFloatFormat;
use Misery\Component\Format\StringToIntFormat;
use Misery\Component\Format\StringToSerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;
use Misery\Component\Format\StringToBooleanFormat;
use Misery\Component\Format\StringToDatetimeFormat;
use Misery\Component\Format\StringToListFormat;
use Misery\Component\Modifier\ArrayUnflattenModifier;
use Misery\Component\Modifier\NullifyEmptyStringModifier;

require __DIR__.'/../vendor/autoload.php';

$parser = CsvParser::create(__DIR__ . '/private/family.csv', ';');
$newFile = CsvParser::create(__DIR__ . '/private/family_new.csv', ';');

$modifierRegistry = new Registry('modifier');
$modifierRegistry
    ->register(StripSlashesModifier::NAME, new StripSlashesModifier())
    ->register(ArrayUnflattenModifier::NAME, new ArrayUnflattenModifier())
    ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
;

$formatRegistry = new Registry('format');
$formatRegistry
    ->register(StringToSerializeFormat::NAME, new StringToSerializeFormat())
    ->register(StringToFloatFormat::NAME, new StringToFloatFormat())
    ->register(StringToIntFormat::NAME, new StringToIntFormat())
    ->register(StringToBooleanFormat::NAME, new StringToBooleanFormat())
    ->register(StringToDatetimeFormat::NAME, new StringToDatetimeFormat())
    ->register(StringToListFormat::NAME, new StringToListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/private/family.yaml'));
$parser->setProcessor($processor);
$newFile->setProcessor($processor);

$compare = new Misery\Component\Csv\Compare\ItemCompare(
   new Misery\Component\Reader\ItemReader($parser),
   new Misery\Component\Reader\ItemReader($newFile)
);

dump(
    $compare->compare('code')
);