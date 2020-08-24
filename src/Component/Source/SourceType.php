<?php

namespace Misery\Component\Source;

class SourceType
{
    public const FILE='file';
    public const STREAM='stream';
    public const API='api';

    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function file()
    {
        return new self(SourceType::FILE);
    }

    public static function stream()
    {
        return new self(SourceType::STREAM);
    }

    public static function api()
    {
        return new self(SourceType::API);
    }

    public function is(string $type): bool
    {
        return $type === $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}