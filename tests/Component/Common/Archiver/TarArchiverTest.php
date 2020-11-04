<?php

namespace Tests\Misery\Component\Common\Archiver;

use Misery\Component\Common\Archiver\CompressedTarArchiver;
use Misery\Component\Common\FileManager\LocalFileManager;
use PHPUnit\Framework\TestCase;

class TarArchiverTest extends TestCase
{
    public function test_it_should_compress(): void
    {
        $manager = new LocalFileManager(__DIR__.DIRECTORY_SEPARATOR.'data');
        $manager = $manager->createSub(uniqid());

        $archiver = new CompressedTarArchiver($manager);

        $manager->addFile('test.nothing', 'NO_CONTENT');

        $archiver->compress('test.tar.gz');

        self::assertCount(1, $manager->listFiles());

        self::assertFileNotExists($manager->getAbsolutePath('test.nothing'));
        self::assertFileExists($manager->getAbsolutePath('test.tar.gz'));

        $manager->removeFile('test.tar.gz');
    }

    public function test_it_should_decompress(): void
    {
        $manager = new LocalFileManager(__DIR__.DIRECTORY_SEPARATOR.'data');
        $manager = $manager->createSub(uniqid());

        $archiver = new CompressedTarArchiver($manager);

        $manager->addFile('test.nothing', 'NO_CONTENT');

        $archiver->compress('test.tar.gz');

        self::assertFileNotExists($manager->getAbsolutePath('test.nothing'));

        // we need to move the file else the archiver sees it as a double from creation
        // ERROR : BadMethodCallException: Unable to add newly converted phar "test.tar" to the list of phars,
        $manager->moveFile('test.tar.gz', 'new.tar.gz');

        $archiver->decompress('new.tar.gz');

        self::assertFileExists($manager->getAbsolutePath('test.nothing'));

        $manager->removeFile('test.nothing');
    }
}