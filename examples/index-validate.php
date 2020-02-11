<?php

require __DIR__.'/../vendor/autoload.php';

$validationDir = __DIR__.'/akeneo/validation';
$exampleDir = __DIR__.'/akeneo/icecat_demo_dev';

$collector = new Misery\Component\Validator\ValidationCollector();

$validationRegistry = new Misery\Component\Common\Registry\Registry();
$validationRegistry->registerNamedObject(new Misery\Component\Csv\Validator\ReferencedColumnValidator($collector));
$validationRegistry->registerNamedObject(new Misery\Component\Csv\Validator\UniqueValueValidator($collector));
$validationRegistry->registerNamedObject(new Misery\Component\Validator\RequiredValidator($collector));
$validationRegistry->registerNamedObject(new Misery\Component\Validator\InArrayValidator($collector));
$validationRegistry->registerNamedObject(new Misery\Component\Validator\SnakeCaseValidator($collector));
$validationRegistry->registerNamedObject(new Misery\Component\Validator\IntegerValidator($collector));

$readerRegistry = new Misery\Component\Common\Registry\Registry();

$processor = new Misery\Component\Common\Processor\CsvValidationProcessor();
$processor
    ->addRegistry($validationRegistry)
    ->addRegistry($readerRegistry)
;

$finder = new Symfony\Component\Finder\Finder();

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($finder->in($exampleDir)->name('*.csv') as $file) {
    $foundFile = str_replace('.'.$file->getExtension(), '', $file->getFilename());

    $reader = new Misery\Component\Reader\ItemReader(
        \Misery\Component\Parser\CsvParser::create($file->getRealPath(),';')
    );
    $readerRegistry->register($reader, $foundFile);

    $processor->filterSubjects(
        Symfony\Component\Yaml\Yaml::parseFile($validationDir . DIRECTORY_SEPARATOR. $foundFile.'.yaml'),
        $foundFile
    );
}

$processor->processValidation();

var_dump(
    $collector->getErrors()
);