<?php

require __DIR__.'/../vendor/autoload.php';

$modifierRegistry = new Misery\Component\Common\Registry\ModifierRegistry();
$modifierRegistry
    ->register(new Misery\Component\Modifier\StripSlashesModifier())
//    ->register(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new Misery\Component\Common\Registry\FormatRegistry();
$formatRegistry
    ->register(new Misery\Component\Format\SerializeFormat())
    ->register(new Misery\Component\Format\FloatFormat())
    ->register(new Misery\Component\Format\IntFormat())
    ->register(new Misery\Component\Format\BooleanFormat())
    ->register(new Misery\Component\Format\DateTimeFormat())
    ->register(new Misery\Component\Format\ListFormat())
;

$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
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