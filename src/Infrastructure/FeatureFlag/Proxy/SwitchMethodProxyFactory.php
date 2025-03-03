<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchMethod;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ApplicationLoggerInterface;

readonly class SwitchMethodProxyFactory
{
    public function __construct(
        private ApplicationLoggerInterface $logger,
        private FeatureFlagProvider $featureFlagProvider,
    ) {}

    public function createProxy(object $service): object
    {
        $factory = new LazyLoadingValueHolderFactory();
        $reflection = new \ReflectionClass($service);

        return $factory->createProxy(
            $service::class,
            /**
             * @phpstan-param object|null $wrappedObject
             * @phpstan-param VirtualProxyInterface $proxy
             * @phpstan-param string $method
             * @phpstan-param array<string, mixed> $parameters
             **/
            function (
                ?object &$wrappedObject = null,
                ?VirtualProxyInterface $proxy = null,
                string $method = '',
                array $parameters = [],
            ) use (
                $service,
                $reflection
            ) {
                $wrappedObject = $service;

                $reflectionMethod = $reflection->getMethod($method);
                $attribute = $reflectionMethod->getAttributes(FeatureFlagSwitchMethod::class)[0] ?? null;

                if (!$attribute) {
                    return $reflectionMethod->invokeArgs($wrappedObject, $parameters);
                }

                /** @var FeatureFlagSwitchMethod $switchMethodConfig */
                $switchMethodConfig = $attribute->newInstance();
                $feature = $switchMethodConfig->feature;
                $alternativeMethod = $switchMethodConfig->method;

                if ($this->featureFlagProvider->provideStateByFlag($feature)) {
                    $this->logger->info("Switching method to: $alternativeMethod");

                    return $service->$alternativeMethod(...$parameters);
                }

                return $reflectionMethod->invokeArgs($wrappedObject, $parameters);
            }
        );
    }
}