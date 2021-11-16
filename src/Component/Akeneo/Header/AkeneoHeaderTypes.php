<?php

namespace Misery\Component\Akeneo\Header;

class AkeneoHeaderTypes implements HeaderTypes
{
    public const SELECT = 'pim_catalog_select';
    public const MULTISELECT = 'pim_catalog_multiselect';
    public const METRIC = 'pim_catalog_metric';
    public const PRICE = 'pim_catalog_price_collection';
}