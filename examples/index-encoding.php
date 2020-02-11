<?php

use Misery\Component\Parser\CsvParser;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Format\BooleanFormat;
use Misery\Component\Format\DateTimeFormat;
use Misery\Component\Format\ListFormat;
use Misery\Component\Format\StringFormat;
use Misery\Component\Modifier\ArrayUnflattenModifier;
use Misery\Component\Modifier\NullifyEmptyStringModifier;
use Misery\Component\Modifier\StripSlashesModifier;

$formatRegistry = new Registry();
$formatRegistry
    ->register(SerializeFormat::NAME, new SerializeFormat())
    ->register(FloatFormat::NAME, new FloatFormat())
    ->register(IntFormat::NAME, new IntFormat())
    ->register(BooleanFormat::NAME, new BooleanFormat())
    ->register(DateTimeFormat::NAME, new DateTimeFormat())
    ->register(ListFormat::NAME, new ListFormat())
    ->register(StringFormat::NAME, new StringFormat())
;

$modifierRegistry = new Registry();
$modifierRegistry
    ->register(StripSlashesModifier::NAME, new StripSlashesModifier())
    ->register(ArrayUnflattenModifier::NAME, new ArrayUnflattenModifier())
    ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
;

require __DIR__.'/../vendor/autoload.php';

// parse Csv

$parser = CsvParser::create(__DIR__ . '/akeneo/icecat_demo_dev/families.csv', ';');

//  To format (electronic data) according to a standard format.

$encoder = new Misery\Component\Csv\Encoder\CsvEncoder($formatRegistry, $modifierRegistry);

// apply format context

$context = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/akeneo/validation/families.yaml');

// iterate data
foreach ($parser->getIterator() as $row) {
    $encodedData = $encoder->encode($row, $context);
    var_dump($encodedData, $row);
    exit;
}
