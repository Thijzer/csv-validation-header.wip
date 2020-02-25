<?php

use Misery\Component\Common\Registry\FormatRegistryInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Format\StringToFloatFormat;
use Misery\Component\Format\StringToIntFormat;
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
    ->register(new StringToFloatFormat())
    ->register(new StringToIntFormat())
    ->register(new Misery\Component\Format\StringToBooleanFormat())
    ->register(new Misery\Component\Format\StringToDatetimeFormat())
    ->register(new Misery\Component\Format\StringToListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;