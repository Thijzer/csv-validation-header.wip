<?php

use Misery\Component\Common\Registry\FormatRegistryInterface;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Format\FloatFormat;
use Misery\Component\Format\IntFormat;
use Misery\Component\Format\SerializeFormat;
use Misery\Component\Modifier\StripSlashesModifier;
use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';

$rootDir = __DIR__ . '/tmp/magento';
$parser = CsvParser::create($rootDir.'/data/acaza-new', ';');
$newFile = CsvParser::create(__DIR__ . '/private/family_new.csv', ';');


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
foreach ($finder1->name('*.csv')->in($files[$repository1]) as $file) {
    $linkedFiles[$file->getFilename()] = [
        'reference' => $references[$file->getFilename()],
        'repository1' => $file->getRealPath(),
        'repository2' => implode(DIRECTORY_SEPARATOR, [$files[$repository2], $file->getFilename()]),
    ];
}
$result = [];
foreach ($linkedFiles as $filename => $file) {
    $reader1 = new CsvReader(CachedCursor::create(CsvParser::create($file['repository1'], ';')));
    $reader2 = new CsvReader(CachedCursor::create(CsvParser::create($file['repository2'], ';')));
    $compare = new CsvCompare($reader1, $reader2);
    $result[$filename] = array_filter($compare->compare($file['reference']));
}