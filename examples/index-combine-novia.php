<?php

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Csv\Writer\CsvWriter;
use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';

$combine = new \Misery\Component\Combine\ItemCombine();

$finder = new Finder();
$finder->in(__DIR__.'/tmp/novia/')->name('*.csv')->sortByName();

// regular cursor, don't use the Cached version or you will need to clear in the loop
// it also has no added benefit as the cursor restart fresh on every file.
// the compare tool only iterates ones , joined file per file.
$newFile = __DIR__ . '/tmp/new_novia_export.csv';
$pool = new ItemCollection();

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($finder as $index => $file) {
    dump($file->getFilename());

    $combine->join(
        $pool,
        CachedCursor::create(CsvParser::create($file->getRealPath(), ';')),
        $reference = 'a_workcode',
        function ($row) use ($pool, $reference) {
            $pool->set($row[$reference], $row);
        }
    );
}

$csvWriter = new CsvWriter($newFile);
$csvWriter->clear();

foreach ($pool->getIterator() as $row) {
    $csvWriter->write($row);
}
