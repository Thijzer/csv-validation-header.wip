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
    ->registerNamedObject(new Misery\Component\Format\SerializeFormat())
    ->registerNamedObject(new Misery\Component\Format\FloatFormat())
    ->registerNamedObject(new Misery\Component\Format\IntFormat())
    ->registerNamedObject(new Misery\Component\Format\BooleanFormat())
    ->registerNamedObject(new Misery\Component\Format\DateTimeFormat())
    ->registerNamedObject(new Misery\Component\Format\ListFormat())
;

$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->registerByName($formatRegistry)
    ->registerByName($modifierRegistry)
;
$processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/akeneo/validation/products.yaml'));

$reader = new Misery\Component\Csv\Reader\CsvReader(
    $parser = Misery\Component\Csv\Reader\CsvParser::create(__DIR__ . '/akeneo/icecat_demo_dev/products.csv', ';')
);
$parser->setProcessor($processor);

$found = $reader->findBy(['family' => 'led_tvs', 'display_diagonal' => '26']);

// unflatten overlap issue
//  "display_diagonal" => "26"
//  "display_diagonal-unit" => "INCH"

dump(
    count($found),
    array_filter(current($found))
);