<?php

require __DIR__.'/vendor/autoload.php';

$exampleFile = __DIR__. '/example_no_row.csv';

$reader = new Component\Csv\Reader\CsvReader();
$filter = new Component\Csv\Filter\CsvDataFilter();
$fetcher = new Component\Csv\Fetcher\CellDataFetcher($reader, $filter);

// join(code, catalog_brand, X)
$data = $fetcher->fetch($exampleFile, $code = 'brand', $x ='puma');
var_dump($data);