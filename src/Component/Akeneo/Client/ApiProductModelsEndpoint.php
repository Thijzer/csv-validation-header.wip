<?php

namespace Misery\Component\Akeneo\Client;

use Misery\Component\Common\Client\ApiEndpointInterface;

class ApiProductModelsEndpoint implements ApiEndpointInterface
{
    public const NAME = 'product_models';
    private const ALL = 'product_models';
    private const ONE = 'product_models/%s';

    public function getAll(): string
    {
        return self::ALL;
    }

    public function getSingleEndPoint(): string
    {
        return self::ONE;
    }
}