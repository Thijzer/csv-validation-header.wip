<?php

namespace Misery\Component\Common\Archiver;

interface ArchiverInterface
{
    public function compress(string $filePath);

    /**
     * returns absolute file list
     *
     * @param string $filePath
     *
     * @return array
     */
    public function decompress(string $filePath);
}