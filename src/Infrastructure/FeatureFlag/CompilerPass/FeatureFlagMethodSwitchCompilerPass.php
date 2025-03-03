<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchMethod;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy\SwitchMethodProxyFactory;

class FeatureFlagMethodSwitchCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(SwitchMethodProxyFactory::class)) {
            return;
        }

        $factoryRef = new Reference(SwitchMethodProxyFactory::class);

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            foreach ($reflection->getMethods() as $method) {
                if (!empty($method->getAttributes(FeatureFlagSwitchMethod::class))) {
                    $definition->setFactory([$factoryRef, 'createProxy']);
                    break;
                }
            }
        }
    }
}