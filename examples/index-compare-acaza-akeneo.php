<?php

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Csv\Compare\ItemCompare;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Csv\Writer\CsvWriter;
use Symfony\Component\Finder\Finder;

require_once __DIR__.'/bootstrap.php';

$rootDir = __DIR__ . '/tmp/acaza';
$project = 'acaza';
$new = $rootDir.'/data/'.$project.'-new';
$old = $rootDir.'/data/'.$project.'-old';

$references = [
    'products.csv' => 'sku',
    'attribute_groups.csv' => 'code',
    'currencies.csv' => 'code',
    'association_types.csv' => 'code',
    'groups.csv' => 'code',
    'channels.csv' => 'code',
    'family_variants.csv' => 'code',
    'users.csv' => 'username',
    'user_roles.csv' => 'role',
    'categories.csv' => 'code',
    'attribute_options.csv' => 'code',
    'attributes.csv' => 'code',
    'families.csv' => 'code',
    'user_groups.csv' => 'code',
    'group_types.csv' => 'code',
    'product_models.csv' => 'code',
    'locales.csv' => 'code',
    'enabled_locales.csv' => 'code',
];

/** @var SplFileInfo $file */
$finder1 = new Finder();
$linkedFiles = [];
$finder1->name('*.csv')->in($old)->sort(function (SplFileInfo $a, SplFileInfo $b) {
   return $a->getSize() > $b->getSize();
});

foreach ($finder1 as $file) {
    $path = $file->getRealPath();
    $pathInfo = pathinfo($path);
    $linkedFiles[$file->getFilename()] = [
        'reference' => $references[$file->getFilename()],
        'validation' =>  __DIR__.'/akeneo/validation/' . $pathInfo['filename'].'.yaml',
        'compare' => $rootDir. '/compare/' . $project . '/' . $pathInfo['filename'].'.yml',
        'repository1' => $file->getRealPath(),
        'repository2' => implode(DIRECTORY_SEPARATOR, [$new, $file->getFilename()]),
    ];
}

foreach ($linkedFiles as $filename => $file) {
    dump($filename);

    if (false === is_file($file['validation'])) {
        continue;
    }

    if (is_file($file['compare'])) {
        continue;
    }

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
        $readerB,
        ['special_from_date', 'special_to_date']
    );
    $data = $compare->compare($reference);

    file_put_contents($file['compare'], json_encode($data));
}
