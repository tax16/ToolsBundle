<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use ReflectionMethod;
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
        return new class($service, $this->logger, $this->featureFlagProvider) {
            private object $wrappedObject;
            private ApplicationLoggerInterface $logger;
            private FeatureFlagProvider $featureFlagProvider;

            public function __construct(object $service, ApplicationLoggerInterface $logger, FeatureFlagProvider $featureFlagProvider)
            {
                $this->wrappedObject = $service;
                $this->logger = $logger;
                $this->featureFlagProvider = $featureFlagProvider;
            }

            public function __call(string $method, array $parameters)
            {
                $reflection = new \ReflectionClass($this->wrappedObject);

                if (!$reflection->hasMethod($method)) {
                    throw new \BadMethodCallException("The method $method does not exist");
                }

                $reflectionMethod = $reflection->getMethod($method);

                $attribute = $reflectionMethod->getAttributes(FeatureFlagSwitchMethod::class)[0] ?? null;

                if ($attribute) {
                    /** @var FeatureFlagSwitchMethod $switchMethodConfig */
                    $switchMethodConfig = $attribute->newInstance();
                    $feature = $switchMethodConfig->feature;
                    $alternativeMethod = $switchMethodConfig->method;

                    if ($this->featureFlagProvider->provideStateByFlag($feature)) {
                        $this->logger->info("Switching method to: ".(string)$alternativeMethod);

                        if ($this->areParametersCompatible($reflectionMethod, $alternativeMethod)) {
                            if (method_exists($this->wrappedObject, $alternativeMethod)) {
                                return $this->wrappedObject->$alternativeMethod(...$parameters);
                            }

                            throw new \BadMethodCallException("The methode $alternativeMethod does not exist");
                        }
                        throw new \InvalidArgumentException("The parameters of the method '$method' and the alternative method '$alternativeMethod' are not compatible.");
                    }
                }

                return $reflectionMethod->invokeArgs($this->wrappedObject, $parameters);
            }

            private function areParametersCompatible(\ReflectionMethod $originalMethod, string $alternativeMethod): bool
            {
                $originalParams = $originalMethod->getParameters();
                $alternativeParams = (new ReflectionMethod($this->wrappedObject, $alternativeMethod))->getParameters();

                if (count($originalParams) !== count($alternativeParams)) {
                    return false;
                }

                foreach ($originalParams as $index => $originalParam) {
                    $alternativeParam = $alternativeParams[$index];

                    if ($originalParam->getType() !== $alternativeParam->getType()) {
                        return false;
                    }
                }

                return true;
            }
        };
    }


}