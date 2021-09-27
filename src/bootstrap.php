<?php

require __DIR__.'/../vendor/autoload.php';

// formatters are reversable // Modifiers are NOT reversable

$sourceRegistry = new Misery\Component\Common\Registry\Registry('source_command');
$sourceRegistry->registerAllByName(
    new Misery\Component\Source\Command\SourceFilterCommand(),
    new Misery\Component\Source\Command\SourceKeyValueCommand(),
);

$converterRegistry = new Misery\Component\Common\Registry\Registry('converter');
$converterRegistry->registerAllByName(
    new Misery\Component\Converter\AkeneoCsvToStructuredDataConverter(
        new Misery\Component\Converter\AkeneoCsvHeaderContext()
    ),
    new Misery\Component\Converter\XmlDataConverter(),
    new Misery\Component\Converter\KliumCsvToStructuredDataConverter(),
    new Misery\Component\Converter\KliumCsvToAttributeStructuredDataConverter()
);

$modifierRegistry = new Misery\Component\Common\Registry\Registry('modifier');
$modifierRegistry
    ->register(Misery\Component\Modifier\StripSlashesModifier::NAME, new Misery\Component\Modifier\StripSlashesModifier())
    ->register(Misery\Component\Modifier\ArrayUnflattenModifier::NAME, new Misery\Component\Modifier\ArrayUnflattenModifier())
    ->register(Misery\Component\Modifier\NullifyEmptyStringModifier::NAME, new Misery\Component\Modifier\NullifyEmptyStringModifier())
    ->register(Misery\Component\Modifier\ReplaceCharacterModifier::NAME, new Misery\Component\Modifier\ReplaceCharacterModifier())
    ->register(Misery\Component\Modifier\FilterEmptyStringModifier::NAME, new Misery\Component\Modifier\FilterEmptyStringModifier())
    ->register(Misery\Component\Modifier\FilterWhiteSpacesModifier::NAME, new Misery\Component\Modifier\FilterWhiteSpacesModifier())
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
    ->register(Misery\Component\Action\SetValueAction::NAME, $setValueAction = new Misery\Component\Action\SetValueAction())
    ->register(Misery\Component\Action\ReseatAction::NAME, new Misery\Component\Action\ReseatAction())
    ->register(Misery\Component\Action\ModifierAction::NAME, new Misery\Component\Action\ModifierAction($modifierRegistry))
    ->register(Misery\Component\Action\BindAction::NAME, new Misery\Component\Action\BindAction())
    ->register(Misery\Component\Action\KeyMapperAction::NAME, new Misery\Component\Action\KeyMapperAction())
    ->register(Misery\Component\Action\ExpandAction::NAME, new Misery\Component\Action\ExpandAction())
    ->register(Misery\Component\Action\StatementAction::NAME, new Misery\Component\Action\StatementAction())
;

#$statementRegistry = new Misery\Component\Common\Registry\Registry('statement');
#$statementRegistry->register(Misery\Component\Statement\EqualsStatement::NAME, Misery\Component\Statement\EqualsStatement::prepare($setValueAction));
#$statementRegistry->register(Misery\Component\Statement\ContainsStatement::NAME, Misery\Component\Statement\ContainsStatement::prepare($setValueAction));

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

$converter = new Misery\Component\Converter\ConverterFactory();
$converter->addRegistry($converterRegistry);

$list = new Misery\Component\Source\ListFactory();
$list->addRegistry($sourceRegistry);

$filter = new Misery\Component\Source\SourceFilterFactory();
$filter->addRegistry($sourceRegistry);

#$statementFactory = new Misery\Component\Statement\StatementFactory();
#$statementFactory
#    ->addRegistry($actionRegistry)
#    ->addRegistry($statementRegistry)
#;

$factoryRegistry = new Misery\Component\Common\Registry\Registry('factories');
$factoryRegistry->registerAllByName(
    new Misery\Component\Source\SourceCollectionFactory(),
    new Misery\Component\Common\Pipeline\PipelineFactory(),
    new Misery\Component\BluePrint\BluePrintFactory(__DIR__.'/../src/BluePrint'),
    new Misery\Component\Statement\StatementFactory(),
    new Misery\Component\Reader\ItemReaderFactory(),
    new Misery\Component\Parser\ItemParserFactory(),
    new Misery\Component\Writer\ItemWriterFactory(),
    new Misery\Component\Common\Cursor\CursorFactory(),
    $list,
    $filter,
    $converter,
    $decoder,
    $encoder,
    $actions
);
$configurationFactory = new Misery\Component\Configurator\ConfigurationFactory($factoryRegistry);
