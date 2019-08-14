<?php

require __DIR__.'/vendor/autoload.php';


// snake_case
$test1 = 'jennyFromTheBlock';

$collector = new Component\Validator\ValidationCollector();
$validator = new Component\Validator\SnakeCaseValidator($collector);

$validator->validate($test1);

var_dump($collector->hasConstraints(), $collector->getMessages());

// snake_case
$test1 = 'jenny_from_the_block';

$collector = new Component\Validator\ValidationCollector();
$validator = new Component\Validator\SnakeCaseValidator($collector);

$validator->validate($test1);

var_dump($collector->hasConstraints(), $collector->getMessages());