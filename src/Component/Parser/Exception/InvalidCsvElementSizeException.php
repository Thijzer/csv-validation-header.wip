<?php

namespace Misery\Component\Parser\Exception;

use Throwable;

class InvalidCsvElementSizeException extends \Exception
{
    public function __construct(string $filename, int $line, array $item, array $headers = null, Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'Invalid Csv Element size on file : %s line : %s header : %s item : %s',
            $filename,
            $line,
            json_encode($headers),
            json_encode($item)
        ), 0, $previous);
    }
}