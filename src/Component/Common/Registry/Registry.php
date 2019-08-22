<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;

interface Registry
{
    public function filterByName($names): ArrayCollection;
}