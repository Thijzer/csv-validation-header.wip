<?php

use Misery\Component\Common\Registry\FormatRegistry;
use Misery\Component\Common\Registry\ModifierRegistry;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Format\FloatToStringFormat;
use Misery\Component\Format\IntToStringFormat;
use Misery\Component\Format\StringToSerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;

require __DIR__.'/../vendor/autoload.php';

$rootDir = __DIR__ . '/tmp/magento';
$parser = CsvParser::create($rootDir.'/data/acaza-new', ';');
$newFile = CsvParser::create(__DIR__ . '/private/family_new.csv', ';');

$modifierRegistry = new Misery\Component\Common\Registry\Registry();
$modifierRegistry
    ->registerNamedObject(new StripSlashesModifier())
    ->registerNamedObject(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->registerNamedObject(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new Misery\Component\Common\Registry\Registry();
$formatRegistry
    ->registerNamedObject(new StringToSerializeFormat())
    ->registerNamedObject(new FloatToStringFormat())
    ->registerNamedObject(new IntToStringFormat())
    ->registerNamedObject(new Misery\Component\Format\BooleanToStringFormat())
    ->registerNamedObject(new Misery\Component\Format\DateTimeToStringFormat())
    ->registerNamedObject(new Misery\Component\Format\ListStringFormat())
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
   new Misery\Component\Reader\CsvReader($parser),
   new Misery\Component\Reader\CsvReader($newFile)
);

dump(
    $compare->compare('code')
);