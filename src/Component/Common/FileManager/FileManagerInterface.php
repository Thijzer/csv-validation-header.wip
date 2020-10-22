<?php

namespace Misery\Component\Common\FileManager;

interface FileManagerInterface
{
    public function addFile(string $filename, $content);
    public function listFiles(): \Generator;
}