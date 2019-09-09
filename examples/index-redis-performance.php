<?php

use Misery\Component\Common\Cache\Redis\RedisAccount;
use Misery\Component\Common\Cache\Redis\RedisCacheFactory;
use Misery\Component\Common\Cache\Redis\RedisNameSpacedCache;
use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Csv\Reader\CsvParser;
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

$modifierRegistry = new Misery\Component\Common\Registry\ModifierRegistry();
$modifierRegistry
    ->register(new Misery\Component\Modifier\StripSlashesModifier())
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

$redisFactory = new RedisCacheFactory();
$redisAccount = new RedisAccount('redis');
$cachePool = new RedisNameSpacedCache($redisFactory->create($redisAccount), 'sales-records');

/** @var SplFileInfo $file */
foreach ($finder->in($path)->name('*.csv') as $file) {
    $reader = new Misery\Component\Csv\Reader\CsvReader(
        CachedCursor::create($parser = CsvParser::create($file->getRealPath(),',')),
    );
    #$reader->indexColumn('Order ID');
    $reader->setCache($cachePool);
    $parser->setProcessor($processor);

    $found = $reader->findBy(['Order ID' => '873970830']);
    var_dump($found);
    exit;
}