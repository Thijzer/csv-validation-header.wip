<?php

require __DIR__.'/../vendor/autoload.php';

$validationDir = __DIR__.'/akeneo/validation';
$exampleDir = __DIR__.'/akeneo/icecat_demo_dev';

$collector = new Misery\Component\Validator\ValidationCollector();

$ValidationRegistry = new Misery\Component\Common\Registry\ValidationRegistryInterface();
$ValidationRegistry->register(new Misery\Component\Csv\Validator\ReferencedColumnValidator($collector));
$ValidationRegistry->register(new Misery\Component\Csv\Validator\UniqueValueValidator($collector));
$ValidationRegistry->register(new Misery\Component\Validator\RequiredValidator($collector));
$ValidationRegistry->register(new Misery\Component\Validator\InArrayValidator($collector));
$ValidationRegistry->register(new Misery\Component\Validator\SnakeCaseValidator($collector));
$ValidationRegistry->register(new Misery\Component\Validator\IntegerValidator($collector));

$readerRegistry = new Misery\Component\Common\Registry\ReaderRegistryInterface();

$processor = new Misery\Component\Common\Processor\CsvValidationProcessor();
$processor
    ->addRegistry($ValidationRegistry)
    ->addRegistry($readerRegistry)
;

$finder = new Symfony\Component\Finder\Finder();

/** @var \Symfony\Component\Finder\SplFileInfo $file */
foreach ($finder->in($exampleDir)->name('*.csv') as $file) {
    $foundFile = str_replace('.'.$file->getExtension(), '', $file->getFilename());

    $reader = new Misery\Component\Csv\Reader\CsvReader(
        Misery\Component\Csv\Reader\CsvParser::create($file->getRealPath(),';')
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