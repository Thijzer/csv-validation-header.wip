<?php

namespace Misery\Component\Akeneo\Client;

use Assert\Assert;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\Configuration;
use Misery\Component\Reader\ReaderInterface;
use Misery\Component\Writer\ItemWriterInterface;

class HttpReaderFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(array $configuration, Configuration $config): ReaderInterface
    {
        Assert::that(
            $configuration['type'],
            'type must be filled in.'
        )->notEmpty()->string()->inArray(['rest_api']);

        Assert::that(
            $configuration['account'],
            'account must be filled in.'
        )->notEmpty()->string();

        if ($configuration['type'] === 'rest_api') {
            Assert::that(
                $configuration['endpoint'],
                'endpoint must be filled in.'
            )->notEmpty()->string();

            Assert::that(
                $configuration['method'],
                'method must be filled in.'
            )->notEmpty()->string()->inArray([
                'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'MULTI_PATCH', 'get', 'post', 'put', 'delete', 'patch', 'multi_patch'
            ]);

            $endpoint = $configuration['endpoint'];
            $method = $configuration['method'];
            $context = ['filters' => []];
            $endpointSet = [
                ApiOptionsEndpoint::NAME => ApiOptionsEndpoint::class,
                ApiAttributesEndpoint::NAME => ApiAttributesEndpoint::class,
                ApiProductsEndpoint::NAME => ApiProductsEndpoint::class,
            ];

            if (isset($configuration['identifier_filter_list'])) {
                $context['multiple'] = true;
                $context['list'] = $config->getList($configuration['identifier_filter_list']);
            }

            $endpoint = $endpointSet[$endpoint] ?? null;
            Assert::that(
                $endpoint,
                'endpoint must be valid.'
            )->notNull();

            if (isset($configuration['filters'])) {
                $filters = $configuration['filters'];
                foreach ($filters as $fieldCode => $filterConfig) {
                    foreach ($filterConfig as $filterType => $value) {
                        if ($filterType === 'list') {
                            $context['filters'][$fieldCode] = $config->getList($value);
                        }
                    }
                }
            }

            $context['limiters'] = $configuration['limiters'] ?? [];

            return new ApiReader(
                $config->getAccount($configuration['account']),
                new $endpoint,
                $context + $config->getContext()
            );
        }

        throw new \Exception('Unknown type: ' . $configuration['type']);
    }

    public function getName(): string
    {
        return 'http_reader';
    }
}