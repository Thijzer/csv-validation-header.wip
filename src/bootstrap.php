<?php

require __DIR__.'/../vendor/autoload.php';

// formatters are reversable // Modifiers are NOT reversable

$modifierRegistry = new Misery\Component\Common\Registry\Registry('modifier');
$modifierRegistry
    ->register(Misery\Component\Modifier\StripSlashesModifier::NAME, new Misery\Component\Modifier\StripSlashesModifier())
    ->register(Misery\Component\Modifier\ArrayUnflattenModifier::NAME, new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(Misery\Component\Modifier\NullifyEmptyStringModifier::NAME, new Misery\Component\Modifier\NullifyEmptyStringModifier())
    ->register(Misery\Component\Modifier\ReplaceCharacterModifier::NAME, new Misery\Component\Modifier\ReplaceCharacterModifier())
    ->register(Misery\Component\Modifier\FilterEmptyStringModifier::NAME, new Misery\Component\Modifier\FilterEmptyStringModifier())
;

$formatRegistry = new Misery\Component\Common\Registry\Registry('format');
$formatRegistry
    ->register(Misery\Component\Format\StringToSerializeFormat::NAME, new Misery\Component\Format\StringToSerializeFormat())
    ->register(Misery\Component\Format\StringToFloatFormat::NAME, new Misery\Component\Format\StringToFloatFormat())
    ->register(Misery\Component\Format\StringToIntFormat::NAME, new Misery\Component\Format\StringToIntFormat())
    ->register(Misery\Component\Format\StringToBooleanFormat::NAME, new Misery\Component\Format\StringToBooleanFormat())
    ->register(Misery\Component\Format\StringToDatetimeFormat::NAME, new Misery\Component\Format\StringToDatetimeFormat())
    ->register(Misery\Component\Format\StringToListFormat::NAME, new Misery\Component\Format\StringToListFormat())
    ->register(Misery\Component\Format\ArrayFlattenFormat::NAME, new Misery\Component\Format\ArrayFlattenFormat())
;

$actionRegistry = new Misery\Component\Common\Registry\Registry('action');
$actionRegistry
    ->register(Misery\Component\Action\RenameAction::NAME, new Misery\Component\Action\RenameAction())
    ->register(Misery\Component\Action\RemoveAction::NAME, new Misery\Component\Action\RemoveAction())
    ->register(Misery\Component\Action\CopyAction::NAME, new Misery\Component\Action\CopyAction())
    ->register(Misery\Component\Action\ReplaceAction::NAME, new Misery\Component\Action\ReplaceAction())
    ->register(Misery\Component\Action\RetainAction::NAME, new Misery\Component\Action\RetainAction())
    ->register(Misery\Component\Action\SetValueAction::NAME, new Misery\Component\Action\SetValueAction())
    ->register(Misery\Component\Action\ReseatAction::NAME, new Misery\Component\Action\ReseatAction())
    ->register(Misery\Component\Action\ModifierAction::NAME, new Misery\Component\Action\ModifierAction($modifierRegistry))
;

//$statementRegistry = new Misery\Component\Common\Registry\Registry('statement');
//$statementRegistry->register(Misery\Component\Statement\EqualsStatement::NAME, Misery\Component\Statement\EqualsStatement::prepare($action));
//$statementRegistry->register(Misery\Component\Statement\ContainsStatement::NAME, Misery\Component\Statement\ContainsStatement::prepare($action));

$actions = new Misery\Component\Action\ItemActionProcessorFactory($actionRegistry);

$encoder = new Misery\Component\Encoder\ItemEncoderFactory();
$encoder
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

$decoder = new Misery\Component\Decoder\ItemDecoderFactory();
$decoder
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;

//$statementFactory = new Misery\Component\Statement\StatementFactory();
//$factory
//    ->addRegistry($actionRegistry)
//    ->addRegistry($statementRegistry)
//;
