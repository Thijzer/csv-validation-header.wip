<?php

use Misery\Component\Common\Registry\Registry;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Format\FloatToStringFormat;
use Misery\Component\Format\IntToStringFormat;
use Misery\Component\Format\StringToSerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;
use Misery\Component\Format\BooleanToStringFormat;
use Misery\Component\Format\DateTimeToStringFormat;
use Misery\Component\Format\ListStringFormat;
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
    ->register(FloatToStringFormat::NAME, new FloatToStringFormat())
    ->register(IntToStringFormat::NAME, new IntToStringFormat())
    ->register(BooleanToStringFormat::NAME, new BooleanToStringFormat())
    ->register(DateTimeToStringFormat::NAME, new DateTimeToStringFormat())
    ->register(ListStringFormat::NAME, new ListStringFormat())
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