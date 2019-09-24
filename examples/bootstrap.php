<?php

use Misery\Component\Common\Registry\FormatRegistry;
use Misery\Component\Common\Registry\ModifierRegistry;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;

require __DIR__.'/../vendor/autoload.php';

$modifierRegistry = new ModifierRegistry();
$modifierRegistry
    ->register(new StripSlashesModifier())
    ->register(new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new FormatRegistry();
$formatRegistry
    ->register(new SerializeFormat())
    ->register(new FloatFormat())
    ->register(new IntFormat())
    ->register(new Misery\Component\Format\BooleanFormat())
    ->register(new Misery\Component\Format\DateTimeFormat())
    ->register(new Misery\Component\Format\ListFormat())
;
$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;