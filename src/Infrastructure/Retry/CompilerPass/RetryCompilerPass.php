<?php

namespace Tax16\ToolsBundle\Infrastructure\Retry\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;
use Tax16\ToolsBundle\Infrastructure\Retry\Proxy\RetryProxyFactory;

class RetryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(RetryProxyFactory::class)) {
            return;
        }

        $factoryRef = new Reference(RetryProxyFactory::class);

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getMethods() as $method) {
                if (!empty($method->getAttributes(Retry::class))) {
                    $definition->setFactory([$factoryRef, 'createProxy']);

                    break;
                }
            }
        }
    }
}
