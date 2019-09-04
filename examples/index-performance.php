<?php

use Misery\Component\Common\Processor\CsvValidationProcessor;
use Misery\Component\Common\Registry\ReaderRegistry;
use Misery\Component\Common\Registry\ValidationRegistry;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\CsvReader;
use Misery\Component\Csv\Validator\ReferencedColumnValidator;
use Misery\Component\Csv\Validator\UniqueValueValidator;
use Misery\Component\Validator\InArrayValidator;
use Misery\Component\Validator\IntegerValidator;
use Misery\Component\Validator\RequiredValidator;
use Misery\Component\Validator\SnakeCaseValidator;
use Misery\Component\Validator\ValidationCollector;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

require __DIR__.'/../vendor/autoload.php';

$validationFile = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/sales.yaml');

$zip = new \ZipArchive();
$res = $zip->open($value->getRealPath());

if ($res === TRUE) {
    $zip->extractTo($path = __DIR__ . '/tmp');
    $zip->close();
} else {
    return;
}

$finder = new Finder();

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
$processor->filterSubjects($validationFile);


/** @var SplFileInfo $file */
foreach ($finder->in($path)->name('*.csv') as $file) {

    $reader = new Misery\Component\Csv\Reader\CsvReader(
        $parser = new CsvReader(CsvParser::create($file->getRealPath(),',')),
    );
    $parser->setProcessor($processor);

    $found = $reader->findBy(['Order ID' => '873970830']);
}
