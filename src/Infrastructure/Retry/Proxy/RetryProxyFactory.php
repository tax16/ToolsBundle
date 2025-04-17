<?php

namespace Tax16\ToolsBundle\Infrastructure\Retry\Proxy;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
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
        $factory = new AccessInterceptorValueHolderFactory();
        $interceptors = $this->buildRetryInterceptors($service);

        return $factory->createProxy($service, $interceptors);
    }

    /**
     * @return \Closure[]
     */
    private function buildRetryInterceptors(object $service): array
    {
        $reflection = new \ReflectionClass($service);
        $interceptors = [];

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Retry::class);
            if (empty($attributes)) {
                continue;
            }

            /** @var Retry $retryConfig */
            $retryConfig = $attributes[0]->newInstance();
            $methodName = $method->getName();

            $interceptors[$methodName] = function (
                object $proxy,
                object $instance,
                string $calledMethod,
                array $params,
                bool &$returnEarly,
            ) use ($method, $retryConfig) {
                $attempts = $retryConfig->attempts;
                $delay = $retryConfig->delay;

                for ($i = 0; $i < $attempts; ++$i) {
                    try {
                        $result = $method->invokeArgs($instance, $params);
                        $returnEarly = true;

                        return $result;
                    } catch (\Throwable $e) {
                        $this->logger->warning('Tentative '.($i + 1)."/$attempts failed: ".$e->getMessage());

                        if ($i < $attempts - 1) {
                            usleep($delay * 1000);
                        } else {
                            throw $e;
                        }
                    }
                }

                throw new \RuntimeException("Toutes les tentatives ont échoué pour $calledMethod");
            };
        }

        return $interceptors;
    }
}
