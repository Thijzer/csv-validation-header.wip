<?php

namespace Misery\Component\Source\Command;

interface ExecuteSourceCommandInterface
{
    public function execute();
    public function executeWithOptions(array $options);
}