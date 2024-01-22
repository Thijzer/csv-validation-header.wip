<?php

namespace Misery\Component\Parser;

use Assert\Assertion;
use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Functions\ArrayFunctions;

class CsvBomParser extends CsvParser
{
    /**
     * @var \SplFileObject
     */
    private $file;

    public function __construct(
        \SplFileObject $file,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE,
        string $invalidLines = self::INVALID_STOP
    ) {
        Assertion::file($file->getRealPath());

        $this->file = $file;
        $this->rewindBom();

        parent::__construct($file, $delimiter, $enclosure, $escapeChar, $invalidLines);
    }

    public static function create(
        string $filename,
        string $delimiter = self::DELIMITER,
        string $enclosure = self::ENCLOSURE,
        string $escapeChar = self::ESCAPE,
        string $invalidLines = self::INVALID_STOP
    ): CsvParser {
        return new self(new \SplFileObject($filename), $delimiter, $enclosure, $escapeChar, $invalidLines);
    }

    private function rewindBom(): void
    {
        $bom = $this->file->fread(3);
        if ($bom === "\xEF\xBB\xBF") {
            $this->file->fseek(3);
        }
    }

    public function rewind(): void
    {
        $this->file->rewind();
        $this->rewindBom();
    }

    public function clear(): void
    {
        // TODO: Implement clear() method.
    }
}