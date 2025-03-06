<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\CompilerPass;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchClass;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;

class FeatureFlagClassSwitchCompilerPass implements CompilerPassInterface
{
    /**
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FeatureFlagProvider::class)) {
            return;
        }

        $featureFlagProvider = $container->get(FeatureFlagProvider::class);

        foreach ($container->getDefinitions() as $id => $definition) {
            if (!$definition->getClass() || !class_exists($definition->getClass())) {
                continue;
            }

            $reflection = new ReflectionClass($definition->getClass());

            $attribute = $reflection->getAttributes(FeatureFlagSwitchClass::class)[0] ?? null;
            if (!$attribute) {
                continue;
            }

            /** @var FeatureFlagSwitchClass $switchConfig */
            $switchConfig = $attribute->newInstance();

            if (!class_exists($switchConfig->switchedClass)) {
                throw new \LogicException("The switched class '{$switchConfig->switchedClass}' does not exist.");
            }

            if (!$this->areMethodsCompatible($definition->getClass(), $switchConfig->switchedClass)) {
                throw new \LogicException("The public methods of the original class and the switched class are not compatible.");
            }

            if ($featureFlagProvider->provideStateByFlag($switchConfig->feature)) {
                $definition->setClass($switchConfig->switchedClass);
            }
        }
    }

    /**
     * Compare the public methods of two classes to ensure they are compatible.
     *
     * @param string $originalClass
     * @param string $switchedClass
     * @return bool
     * @throws \ReflectionException
     */
    private function areMethodsCompatible(string $originalClass, string $switchedClass): bool
    {
        $originalReflection = new ReflectionClass($originalClass);
        $switchedReflection = new ReflectionClass($switchedClass);

        $originalMethods = $originalReflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($originalMethods as $originalMethod) {
            if (!$switchedReflection->hasMethod($originalMethod->getName())) {
                return false;
            }

            $switchedMethod = $switchedReflection->getMethod($originalMethod->getName());

            if (!$this->areParametersCompatible($originalMethod, $switchedMethod)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare the parameters of two methods to ensure they are compatible.
     *
     * @param ReflectionMethod $originalMethod
     * @param ReflectionMethod $switchedMethod
     * @return bool
     */
    private function areParametersCompatible(ReflectionMethod $originalMethod, ReflectionMethod $switchedMethod): bool
    {
        $originalParams = $originalMethod->getParameters();
        $switchedParams = $switchedMethod->getParameters();

        if (count($originalParams) !== count($switchedParams)) {
            return false;
        }

        foreach ($originalParams as $index => $originalParam) {
            $switchedParam = $switchedParams[$index];

            if ($originalParam->getType() !== $switchedParam->getType()) {
                return false;
            }

            if ($originalParam->isOptional() !== $switchedParam->isOptional()) {
                return false;
            }
        }

        return true;
    }
}