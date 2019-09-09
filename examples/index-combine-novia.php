<?php

use Misery\Component\Common\Cache\Local\InMemoryCache;
use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Writer\CsvWriter;
use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';

$combine = new Misery\Component\Csv\Combine\CsvCombine();

$finder = new Finder();
$finder->in(__DIR__.'/tmp/novia/')->name('*.csv')->sortByName();

// regular cursor, don't use the Cached version or you will need to clear in the loop
// it also has no added benefit as the cursor restart fresh on every file.
// the compare tool only iterates ones , joined file per file.
$reader = new Misery\Component\Csv\Reader\CsvReader(CsvParser::create($newFile, ';'));

$pool = new InMemoryCache();

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($finder as $index => $file) {

    dump($index);

    $combine->join(
        $reader,
        new Misery\Component\Csv\Reader\CsvReader(CachedCursor::create(CsvParser::create($file->getRealPath(), ';'))),
        'a_workcode',
        function ($row, $key) use ($pool) {
            $pool->set($key, $row);
        }
    );
}

$csvWriter = new CsvWriter($newFile = __DIR__ . '/tmp/new_novia_export.csv');
$csvWriter->clear();

foreach ($pool->getIterator() as $row) {
    $csvWriter->write($row);
}
