<?php

namespace Misery\Component\Action;

use Misery\Component\Akeneo\AkeneoValuePicker;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;
use Psr\Container\ContainerInterface;

class TransformAction implements OptionsInterface, ItemReaderAwareInterface
{
    /** @var ContainerInterface */
    private $container;
    private $service;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    use OptionsTrait;
    use ItemReaderAwareTrait;
    private $repo;

    public const NAME = 'transform';

    /** @var array */
    private $options = [
        'source' => null,
        'source_filter' => [],
        'source_reference' => 'code',
        'credentials' => 'actions:aws_s3_credential_string',
        'method' => 's3_url', # akeneo_url # s3_url_file # s3_url_image
    ];

    public function apply(array $item): array
    {
        if (array_key_exists($this->options['key'], $item)) {
            $label = $this->options['content'];

            switch ($this->options['method']) {

                case "s3_url":
                    $service = $this->getService('action:aws_s3');

                    // get list of the types we need
                    // loop over the list and tranform
                    foreach ($list = [] as $listItem) {
                        $item[$listItem] = $service->transform($item[$listItem]);
                    }
                    break;

                case "akeneo_url":
                    break;

                case "s3_url_file":
                    break;

                case "s3_url_image":
                    break;

                default;
                    break;
            }
        }

        return $item;
    }

    private function getService(string $serviceName = null)
    {
        if (null === $this->service) {
            $serviceName = $serviceName ?? $this->options['service'];
            if ($serviceName && $this->container->has($serviceName)) {
                $this->service = $this->container->get($serviceName);
            }
        }

        return $this->service;
    }
}