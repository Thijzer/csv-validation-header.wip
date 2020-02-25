<?php

require __DIR__.'/../vendor/autoload.php';

$modifierRegistry = new Misery\Component\Common\Registry\Registry();
$modifierRegistry
    ->registerNamedObject(new Misery\Component\Modifier\StripSlashesModifier())
//    ->registerByName(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->registerNamedObject(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new Misery\Component\Common\Registry\Registry();
$formatRegistry
    ->registerNamedObject(new Misery\Component\Format\StringToSerializeFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToFloatFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToIntFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToBooleanFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToDatetimeFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToListFormat())
;

$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->registerByName($formatRegistry)
    ->registerByName($modifierRegistry)
;
$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/akeneo/validation/products.yaml'));

$reader = new Misery\Component\Reader\ItemReader(
    $parser = \Misery\Component\Parser\CsvParser::create(__DIR__ . '/akeneo/icecat_demo_dev/products.csv', ';')
);
$parser->setProcessor($processor);

$found = $reader->find(['family' => 'led_tvs', 'display_diagonal' => '26']);

// unflatten overlap issue
//  "display_diagonal" => "26"
//  "display_diagonal-unit" => "INCH"

dump(
    count($found),
    array_filter(current($found))
);