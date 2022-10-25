<?php

namespace Misery\Component\Common\FileManager;

use Assert\Assert;

class InMemoryFileManager
{
    private $files;

    public static function createFromFileManager(FileManagerInterface $sourceCollection): self
    {
        $fm = new self();
        $fm->addFiles($sourceCollection->listFiles());

        return $fm;
    }

    public function getFile(string $filename): string
    {
        foreach ($this->files as $file => $file_name) {
            if ($filename === $file_name) {
                return $file;
            }
        }
        throw new \Exception(sprintf('File %s not found', $filename));
    }

    public function addFiles($files): void
    {
        Assert::that($files)->isTraversable();

        foreach ($files as $file) {
            $pathInfo = pathInfo($file);
            Assert::that($file)->file();
            $this->files[$file] = $pathInfo['basename'];
        }
    }
}