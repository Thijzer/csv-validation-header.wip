<?php

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Csv\Compare\ItemCompare;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Csv\Writer\CsvWriter;
use Symfony\Component\Finder\Finder;

require_once __DIR__.'/bootstrap.php';

$rootDir = __DIR__ . '/tmp/magento';
$project = 'mobistoxx';
$new = $rootDir.'/data/'.$project.'-new';
$old = $rootDir.'/data/'.$project.'-old';

$references = [
    'magento_attribute_groups.csv' => 'id',
    'magento_attribute_options.csv' => 'id',
    'magento_attribute_sets.csv' => 'id',
    'magento_attributes.csv' => 'id',
    'magento_categories.csv' => 'id',
    'magento_product_files.csv' => 'value_id',
    'magento_products.csv' => 'id',
    'magento_product_values.csv'  => 'product_id',
    'magento_stores.csv' => 'id',
];

/** @var SplFileInfo $file */
$finder1 = new Finder();
$linkedFiles = [];
foreach ($finder1->name('*.csv')->in($old) as $file) {
    $path = $file->getRealPath();
    $pathInfo = pathinfo($path);
    $linkedFiles[$file->getFilename()] = [
        'reference' => $references[$file->getFilename()],
        'validation' => $rootDir. '/validation/' . $pathInfo['filename'].'.yaml',
        'compare' => $rootDir. '/compare/' . $project . '/' . $pathInfo['filename'].'.csv',
        'repository1' => $file->getRealPath(),
        'repository2' => implode(DIRECTORY_SEPARATOR, [$new, $file->getFilename()]),
    ];
}

foreach ($linkedFiles as $filename => $file) {
    $processor->filterSubjects(Symfony\Component\Yaml\Yaml::parseFile($file['validation']));

    $parser1 = CsvParser::create($file['repository1'], ';');
    $parser1->setProcessor($processor);

    $parser2 = CsvParser::create($file['repository2'], ';');
    $parser2->setProcessor($processor);

    $reference = $file['reference'];

    $readerA = new Misery\Component\Reader\ItemReader(CachedCursor::create($parser1));
    $readerA->indexColumn($reference);

    $readerB = new Misery\Component\Reader\ItemReader(CachedCursor::create($parser2));
    $readerB->indexColumn($reference);

    $compare = new Misery\Component\Csv\Compare\ItemCompare(
        $readerA,
        $readerB
    );
    $dump = $compare->compare($reference);
    file_put_contents($file['compare'], json_encode([
        ItemCompare::ADDED => \count($dump[ItemCompare::ADDED]),
        ItemCompare::CHANGED => \count($dump[ItemCompare::CHANGED]),
        ItemCompare::REMOVED => \count($dump[ItemCompare::REMOVED]),
    ]));
}
