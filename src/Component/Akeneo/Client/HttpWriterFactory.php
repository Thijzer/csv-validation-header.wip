<?php

namespace Misery\Component\Akeneo\Client;

use Assert\Assert;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\Configuration;
use Misery\Component\Writer\ItemWriterInterface;

class HttpWriterFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(array $configuration, Configuration $config): ItemWriterInterface
    {
        Assert::that(
            $configuration['type'],
            'type must be filled in.'
        )->notEmpty()->string()->inArray(['rest_api']);

        if ($configuration['type'] === 'rest_api') {
            Assert::that(
                $configuration['endpoint'],
                'endpoint must be filled in.'
            )->notEmpty()->string();

            Assert::that(
                $configuration['method'],
                'method must be filled in.'
            )->notEmpty()->string()->inArray([
                'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'MULTI_PATCH', 'get', 'post', 'put', 'delete', 'patch', 'multi_patch',
            ]);

            $endpoint = $configuration['endpoint'];
            $method = $configuration['method'];

            $endpointSet = [
                ApiAttributesEndpoint::NAME => ApiAttributesEndpoint::class,
                ApiOptionsEndpoint::NAME => ApiOptionsEndpoint::class,
                ApiProductsEndpoint::NAME => ApiProductsEndpoint::class,
                ApiProductModelsEndpoint::NAME => ApiProductModelsEndpoint::class,
                ApiCategoriesEndpoint::NAME => ApiCategoriesEndpoint::class,
                ApiReferenceEntitiesEndpoint::NAME => ApiReferenceEntitiesEndpoint::class,
                ApiFamiliesEndpoint::NAME => ApiFamiliesEndpoint::class,
                ApiFamilyVariantsEndpoint::NAME => ApiFamilyVariantsEndpoint::class,
            ];

            $endpoint = $endpointSet[$endpoint] ?? null;
            Assert::that(
                $endpoint,
                'endpoint must be valid.'
            )->notNull();

            $accountCode = (isset($configuration['account'])) ? $configuration['account'] : 'target_resource';
            $account = $config->getAccount($accountCode);

            if (!$account) {
                throw new \Exception(sprintf('Account "%s" not found.', $accountCode));
            }

            return new ApiWriter(
                $account,
                new $endpoint,
                $method
            );
        }

        throw new \Exception('Unknown type: ' . $configuration['type']);
    }

    public function getName(): string
    {
        return 'http_writer';
    }
}