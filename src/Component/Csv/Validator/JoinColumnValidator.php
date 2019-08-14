<?php

namespace RFC\Component\Csv\Filter;

class JoinColumnValidator
{
    public function validate($dataStream, $reference, $cellValue): bool
    {
        return \in_array($cellValue, $dataStream->getColumn($reference));
    }
}
