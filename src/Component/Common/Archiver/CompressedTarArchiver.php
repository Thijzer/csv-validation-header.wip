<?php

namespace Misery\Component\Common\Archiver;

use Misery\Component\Common\FileManager\LocalFileManager;

class CompressedTarArchiver implements ArchiverInterface
{
    /** @var LocalFileManager */
    private $fileManager;

    public function __construct(LocalFileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /** @inheritDoc */
    public function compress(string $filePath): void
    {
        $tarPath = $this->getTarPath($filePath);

        $p = new \PharData($tarPath);

        $p->buildFromDirectory($this->fileManager->getWorkingDirectory());

        $p->compress(\Phar::GZ);
    }

    /** @inheritDoc */
    public function decompress(string $filePath): void
    {
        // '/path/to/my.tar.gz'
        $p = new \PharData($filePath);

        // creates /path/to/my.tar
        $p->decompress();

        // unarchived from the tar
        $tarPath = $this->getTarPath($filePath);
        $phar = new \PharData($tarPath);

        $phar->extractTo($this->fileManager->getWorkingDirectory());

        // success then remove residu
        $this->fileManager->removeFile($filePath);
        $this->fileManager->removeFile($tarPath);
    }

    private function getTarPath(string $filePath): string
    {
        return substr($filePath, 0 , (strrpos($filePath, '.')));
    }
}