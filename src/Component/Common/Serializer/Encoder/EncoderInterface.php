<?php

namespace Misery\Component\Common\Serializer\Encoder;

interface EncoderInterface
{
    public function encode($data, string $format, array $context = []);

    public function supports($format): bool;
}