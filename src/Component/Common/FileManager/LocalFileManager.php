<?php

namespace Misery\Component\Common\FileManager;

class LocalFileManager implements FileManagerInterface
{
    private $workingDirectory;
    private $removeEmptyDir;

    public function __construct(string $workingDirectory, bool $removeEmptyDir = true)
    {
        $this->workingDirectory = $workingDirectory;
        $this->removeEmptyDir = $removeEmptyDir;
    }

    public function appendDirectory(string $workingDirectory)
    {
        $directory = $this->workingDirectory . DIRECTORY_SEPARATOR . $workingDirectory;
        $this->makePath($directory);

        return new self($directory);
    }

    /**
     * Only for local file management
     * Cannot have support in the interface
     */
    public function getWorkingDirectory(): string
    {
        return $this->workingDirectory;
    }

    public function addFile(string $filename, $content)
    {
        file_put_contents($this->getAbsolutePath($filename), $content);
    }

    public function getFileContent(string $filename)
    {
        file_get_contents($this->getAbsolutePath($filename));
    }

    public function removeFile(string $filename): void
    {
        unlink($fullPath = $this->getAbsolutePath($filename));

        if ($this->removeEmptyDir) {
            $this->removeEmptyDirectory(pathinfo($fullPath)['dirname']);
        }
    }

    public function listFiles(): \Generator
    {
        foreach (glob($this->workingDirectory."/*") as $file) {
            yield $file;
        }
    }

    /**
     * Making Path's is irrelevant in object storage
     * We make path's if needed
     * @param string $directory
     */
    private function makePath(string $directory)
    {
        if (!file_exists($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }

    /**
     * Returns Absolute paths even when entering Relative ones.
     * Only Local FS required absoluteness
     *
     * @param string $filename
     * @return string
     */
    private function getAbsolutePath(string $filename): string
    {
        if (strpos($filename, $this->workingDirectory) === false) {
            return $this->workingDirectory. DIRECTORY_SEPARATOR . $filename;
        }

        return $filename;
    }

    /**
     * Removes the empty Directory and subdirectory
     * Imitation object storage behavior as we don't care about directory or directory structures.
     *
     * @param string $directory
     *
     * @return bool
     */
    private function removeEmptyDirectory(string $directory): bool
    {
        $empty = true;
        foreach (glob($directory.DIRECTORY_SEPARATOR."*") as $file) {
            $empty &= is_dir($file) && $file !== $this->workingDirectory && $this->removeEmptyDirectory($file);
        }

        return $empty && rmdir($directory);
    }
}