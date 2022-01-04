<?php

namespace Misery\Component\Common\Client;

use Misery\Component\Akeneo\Client\AkeneoApiClientAccount;
use Misery\Component\Common\Registry\RegisteredByNameInterface;

class ApiClientFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(array $account): ApiClient
    {
        try {
            $client = new ApiClient($account['domain']);

            $account = new AkeneoApiClientAccount(
                $account['username'],
                $account['password'],
                $account['client_id'],
                $account['secret']
            );

            $client->authorize($account);

            return $client;
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to create API client', 0, $e);
        }
    }

    public function getName(): string
    {
        return 'api_client';
    }
}