<?php

use Misery\Component\Parser\CsvParser;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Format\FloatToStringFormat;
use Misery\Component\Format\IntToStringFormat;
use Misery\Component\Format\StringToSerializeFormat;
use Misery\Component\Format\BooleanToStringFormat;
use Misery\Component\Format\DateTimeToStringFormat;
use Misery\Component\Format\ListStringFormat;
use Misery\Component\Format\StringToStringFormat;
use Misery\Component\Modifier\ArrayUnflattenModifier;
use Misery\Component\Modifier\NullifyEmptyStringModifier;
use Misery\Component\Modifier\StripSlashesModifier;

$formatRegistry = new Registry('format');
$formatRegistry
    ->register(StringToSerializeFormat::NAME, new StringToSerializeFormat())
    ->register(FloatToStringFormat::NAME, new FloatToStringFormat())
    ->register(IntToStringFormat::NAME, new IntToStringFormat())
    ->register(BooleanToStringFormat::NAME, new BooleanToStringFormat())
    ->register(DateTimeToStringFormat::NAME, new DateTimeToStringFormat())
    ->register(ListStringFormat::NAME, new ListStringFormat())
    ->register(StringToStringFormat::NAME, new StringToStringFormat())
;

$modifierRegistry = new Registry('modifier');
$modifierRegistry
    ->register(StripSlashesModifier::NAME, new StripSlashesModifier())
    ->register(ArrayUnflattenModifier::NAME, new ArrayUnflattenModifier())
    ->register(NullifyEmptyStringModifier::NAME, new NullifyEmptyStringModifier())
;

require __DIR__.'/../vendor/autoload.php';

// parse Csv


$parser = CsvParser::create(__DIR__ . '/akeneo/icecat_demo_dev/families.csv', ';');

//  To format (electronic data) according to a standard format.

$encoder = new Misery\Component\Encoder\ItemEncoder($formatRegistry, $modifierRegistry);

// apply format context

$context = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/akeneo/validation/families.yaml');

// iterate data
foreach ($parser->getIterator() as $row) {
    $encodedData = $encoder->encode($row, $context);
    var_dump($encodedData, $row);
    exit;
}
