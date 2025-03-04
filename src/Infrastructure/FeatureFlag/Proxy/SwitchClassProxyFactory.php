<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use ReflectionClass;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchClass;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;

class SwitchClassProxyFactory
{
    public function __construct(private FeatureFlagProvider $featureFlagService) {}

    public function createProxy(object $service): object
    {
        $factory = new LazyLoadingValueHolderFactory();
        $reflection = new ReflectionClass($service);

        return $factory->createProxy(
            $service::class,
            function (
                ?object &$wrappedObject,
                VirtualProxyInterface $proxy,
                string $method,
                array $parameters
            ) use ($service, $reflection) {
                $wrappedObject = $service;

                $attribute = $reflection->getAttributes(FeatureFlagSwitchClass::class)[0] ?? null;

                if ($attribute) {
                    /** @var FeatureFlagSwitchClass $switchClassConfig */
                    $switchClassConfig = $attribute->newInstance();

                    if ($this->featureFlagService->provideStateByFlag($switchClassConfig->feature)) {
                        $newClass = new ReflectionClass($switchClassConfig->switchedClass);
                        $wrappedObject = $newClass->newInstanceArgs($reflection->getConstructor()?->getParameters() ?? []);
                    }
                }

                return $reflection->getMethod($method)->invokeArgs($wrappedObject, $parameters);
            }
        );
    }
}