<?php

namespace Misery\Component\Common\FileManager;

interface FileManagerInterface
{
    public function addFile(string $filename, $content);
    public function getFileContent(string $filename);
    public function removeFile(string $filename): void;
    public function removeFiles(...$filenames): void;
    public function isFile(string $filename): bool;

    public function listFiles(): \Generator;

    /** remove all files inside the working directory. */
    public function clear(): void;
}