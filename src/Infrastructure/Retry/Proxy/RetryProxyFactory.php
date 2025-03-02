<?php

namespace Tax16\ToolsBundle\Infrastructure\Retry\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use Psr\Log\LoggerInterface;
use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;

readonly class RetryProxyFactory
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

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
                $attribute = $reflectionMethod->getAttributes(Retry::class)[0] ?? null;

                if (!$attribute) {
                    return $reflectionMethod->invokeArgs($wrappedObject, $parameters);
                }

                /** @var Retry $retryConfig */
                $retryConfig = $attribute->newInstance();
                $attempts = $retryConfig->attempts;
                $delay = $retryConfig->delay;

                for ($i = 0; $i < $attempts; ++$i) {
                    try {
                        return $reflectionMethod->invokeArgs($wrappedObject, $parameters);
                    } catch (\Throwable $e) {
                        $this->logger->warning('Tentative '.($i + 1)."/$attempts failed: ".$e->getMessage());

                        if ($i < $attempts - 1) {
                            usleep($delay * 1000);
                        } else {
                            throw $e;
                        }
                    }
                }
                throw new \RuntimeException('All retry attempts failed');
            },
        );
    }
}
