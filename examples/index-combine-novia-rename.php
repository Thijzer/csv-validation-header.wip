<?php

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\ItemCollection;
use Misery\Component\Csv\Writer\CsvWriter;
use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';

$combine = new Misery\Component\Csv\Combine\CsvCombine();

$finder = new Finder();
$finder->in(__DIR__.'/tmp/novia/')->name('*.csv')->sortByName();

// regular cursor, don't use the Cached version or you will need to clear in the loop
// it also has no added benefit as the cursor restart fresh on every file.
// the compare tool only iterates ones , joined file per file.
$newFile = __DIR__ . '/tmp/new_novia_export.csv';
$pool = new ItemCollection();

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($finder as $index => $file) {
    $dates = explode('_',
        str_replace('dbfact_import_export_', '', $file->getFilename())
    );
    $date = \DateTime::createFromFormat('dmY', $dates[0]);
    $newFile = implode('_', array_merge([
        'dbfact_import_export',
        $date->format('Ymd'),
    ], $dates));

    copy($file->getRealPath(), $newFile);
}
