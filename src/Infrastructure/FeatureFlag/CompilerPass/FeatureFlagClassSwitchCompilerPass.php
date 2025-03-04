<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchClass;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy\SwitchClassProxyFactory;

class FeatureFlagClassSwitchCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(SwitchClassProxyFactory::class)) {
            return;
        }

        $proxyFactoryRef = new Reference(SwitchClassProxyFactory::class);

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            $attribute = $reflection->getAttributes(FeatureFlagSwitchClass::class)[0] ?? null;

            if ($attribute) {
                $definition->setFactory([$proxyFactoryRef, 'createProxy']);
            }
        }
    }
}