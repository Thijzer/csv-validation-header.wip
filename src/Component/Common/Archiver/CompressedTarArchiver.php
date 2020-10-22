<?php

namespace Misery\Component\Common\Archiver;

use Misery\Component\Common\FileManager\LocalFileManager;

class CompressedTarArchiver implements ArchiverInterface
{
    /** @var LocalFileManager */
    private LocalFileManager $fileManager;

    public function __construct(LocalFileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public function compress(string $filePath)
    {
    }

    /** @inheritDoc */
    public function decompress(string $filePath): void
    {
        // '/path/to/my.tar.gz'
        $p = new \PharData($filePath);

        // creates /path/to/my.tar
        $p->decompress();

        // unarchive from the tar
        $tarPath = substr($filePath, 0 , (strrpos($filePath, ".")));
        $phar = new \PharData($tarPath);

        $phar->extractTo($this->fileManager->getWorkingDirectory());

        // succes then remove
        $this->fileManager->removeFile($filePath);
        $this->fileManager->removeFile($tarPath);
    }
}