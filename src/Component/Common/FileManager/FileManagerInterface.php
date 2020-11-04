<?php

namespace Misery\Component\Common\FileManager;

interface FileManagerInterface
{
    public function addFile(string $filename, $content);
    public function getFileContent(string $filename);
    public function removeFile(string $filename): void;

    public function listFiles(): \Generator;
}