<?php

namespace Misery\Component\Parser\Exception;

use Throwable;

class InvalidCsvElementSizeException extends \Exception
{
    public function __construct(string $filename, int $line, Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid Csv Element size on file : %s line : %s', $filename, $line), 0, $previous);
    }
}