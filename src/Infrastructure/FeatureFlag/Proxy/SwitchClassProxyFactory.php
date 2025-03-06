<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchClass;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;

class SwitchClassProxyFactory
{

    public function __construct(
        private FeatureFlagProvider $featureFlagProvider,
        private ContainerInterface $container
    ) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(object $originalService): object
    {
        $reflection = new ReflectionClass($originalService);
        $attribute = $reflection->getAttributes(FeatureFlagSwitchClass::class)[0] ?? null;

        if (!$attribute) {
            return $originalService;
        }

        /** @var FeatureFlagSwitchClass $switchConfig */
        $switchConfig = $attribute->newInstance();

        if (!class_exists($switchConfig->switchedClass)) {
            throw new \LogicException("The switched class '{$switchConfig->switchedClass}' does not exist.");
        }

        if ($this->featureFlagProvider->provideStateByFlag($switchConfig->feature)) {
            return $this->container->get($switchConfig->switchedClass);
        }

        return $originalService;
    }
}