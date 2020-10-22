<?php

namespace Misery\Component\Common\FileManager;

class LocalFileManager implements FileManagerInterface
{
    private string $workingDirectory;

    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function appendDirectory(string $workingDirectory)
    {
        $directory = $this->workingDirectory . DIRECTORY_SEPARATOR . $workingDirectory;
        $this->makePath($directory);

        return new self($directory);
    }

    /**
     * Only for local file management
     */
    public function getWorkingDirectory(): string
    {
        return $this->workingDirectory;
    }

    public function addFile(string $filename, $content)
    {
        file_put_contents($this->getPath($filename), $content);
    }

    public function getPath(string $filename): string
    {
        if (strpos($filename, $this->workingDirectory) === false) {
            return $this->workingDirectory. DIRECTORY_SEPARATOR . $filename;
        }

        return $filename;
    }

    public function removeFile(string $filename): void
    {
        unlink($this->getPath($filename));
    }

    public function listFiles(): \Generator
    {
        foreach (glob($this->workingDirectory."/*") as $file) {
            yield $file;
        }
    }

    private function makePath(string $directory)
    {
        if (!file_exists($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }
}