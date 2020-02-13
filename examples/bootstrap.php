<?php

use Misery\Component\Common\Registry\FormatRegistryInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Format\FloatToStringFormat;
use Misery\Component\Format\IntToStringFormat;
use Misery\Component\Format\StringToSerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;

require __DIR__.'/../vendor/autoload.php';

$modifierRegistry = new Registry();
$modifierRegistry
    ->register(new StripSlashesModifier())
    ->register(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new FormatRegistryInterface();
$formatRegistry
    ->register(new StringToSerializeFormat())
    ->register(new FloatToStringFormat())
    ->register(new IntToStringFormat())
    ->register(new Misery\Component\Format\BooleanToStringFormat())
    ->register(new Misery\Component\Format\DateTimeToStringFormat())
    ->register(new Misery\Component\Format\ListStringFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;