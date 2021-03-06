<?php

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\RedisAccount;
use Misery\Component\Common\Cursor\RedisCacheFactory;
use Misery\Component\Common\Cursor\RedisNameSpacedCache;
use Misery\Component\Common\Cursor\SimpleCachedCursor;
use Misery\Component\Parser\CsvParser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

require __DIR__.'/../vendor/autoload.php';

$validationFile = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/sales.yaml');
$path = __DIR__ . '/tmp/';

//$zip = new \ZipArchive();
//$res = $zip->open(__DIR__.'/1500000 Sales Records.zip');
//
//if ($res === true) {
//    $zip->extractTo($path);
//    $zip->close();
//} else {
//    return;
//}

$finder = new Finder();

$modifierRegistry = new Misery\Component\Common\Registry\Registry();
$modifierRegistry
    ->registerNamedObject(new Misery\Component\Modifier\StripSlashesModifier())
    ->registerNamedObject(new Misery\Component\Modifier\NullifyEmptyStringModifier())
;
$formatRegistry = new Misery\Component\Common\Registry\Registry();
$formatRegistry
    ->registerNamedObject(new Misery\Component\Format\StringToSerializeFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToFloatFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToIntFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToBooleanFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToDatetimeFormat())
    ->registerNamedObject(new Misery\Component\Format\StringToListFormat())
;

$processor = new Misery\Component\Common\Processor\CsvDataProcessor();
$processor
    ->addRegistry($formatRegistry)
    ->addRegistry($modifierRegistry)
;
$processor->filterSubjects($validationFile);


/** @var SplFileInfo $file */
foreach ($finder->in($path)->name('*.csv') as $file) {
    $reader = new Misery\Component\Reader\ItemReader(
        CachedCursor::create($parser = CsvParser::create($file->getRealPath(),',')),
    );
    $parser->setProcessor($processor);

    $found = $reader->find(['Order ID' => '873970830']);
    var_dump($found);
    exit;
}
