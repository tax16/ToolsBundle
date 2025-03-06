<?php

namespace App\Tests\Unit\Infrastructure\FeatureFlag\CompilerPass;

use App\Tests\Unit\Infrastructure\FeatureFlag\CompilerPass\FakeClass\FakeService;
use App\Tests\Unit\Infrastructure\FeatureFlag\CompilerPass\FakeClass\FakeServiceSwitch;
use App\Tests\Unit\Infrastructure\FeatureFlag\CompilerPass\FakeClass\FakeServiceSwitched;
use App\Tests\Unit\Infrastructure\FeatureFlag\CompilerPass\FakeClass\FakeServiceSwitchNoMethod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory\FeatureFlagLoaderFactory;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\CompilerPass\FeatureFlagClassSwitchCompilerPass;

class FeatureFlagClassSwitchCompilerPassTest extends TestCase
{
    public function testCompilerPassSwitchesServiceWhenFeatureFlagIsActive(): void
    {
        $container = new ContainerBuilder();

        $featureFlagLoaderFactoryMock = $this->createMock(FeatureFlagLoaderFactory::class);
        $featureFlagLoaderInterfaceMock = $this->createMock(FeatureFlagLoaderInterface::class);
        $featureFlagLoaderInterfaceMock->method('loadFeatureFlags')
            ->willReturn([new FeatureFlag(
                'new_feature', true
            )]);
        $featureFlagLoaderFactoryMock->method('create')
            ->willReturn($featureFlagLoaderInterfaceMock);

        $featureFlagProviderMock = $this->createMock(FeatureFlagProvider::class);

        $featureFlagProviderMock->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $featureFlagProviderDefinition = new Definition(FeatureFlagProvider::class);
        $featureFlagProviderDefinition->setArguments([$featureFlagLoaderFactoryMock]);

        $container->setDefinition(FeatureFlagProvider::class, $featureFlagProviderDefinition);

        $definition = new Definition(FakeService::class);
        $container->setDefinition(FakeService::class, $definition);

        $compilerPass = new FeatureFlagClassSwitchCompilerPass();
        $compilerPass->process($container);

        $this->assertSame(FakeServiceSwitched::class, $container->getDefinition(FakeService::class)->getClass());
    }

    public function testCompilerPassSwitchesServiceWhenFeatureFlagIsInactive(): void
    {
        $container = new ContainerBuilder();

        $featureFlagLoaderFactoryMock = $this->createMock(FeatureFlagLoaderFactory::class);
        $featureFlagLoaderInterfaceMock = $this->createMock(FeatureFlagLoaderInterface::class);

        $featureFlagLoaderInterfaceMock->method('loadFeatureFlags')
            ->willReturn([new FeatureFlag(
                'new_feature', false
            )]);
        $featureFlagLoaderFactoryMock->method('create')
            ->willReturn($featureFlagLoaderInterfaceMock);

        $featureFlagProviderMock = $this->createMock(FeatureFlagProvider::class);
        $featureFlagProviderMock->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(false);

        $featureFlagProviderDefinition = new Definition(FeatureFlagProvider::class);
        $featureFlagProviderDefinition->setArguments([$featureFlagLoaderFactoryMock]);
        $container->setDefinition(FeatureFlagProvider::class, $featureFlagProviderDefinition);

        $definition = new Definition(FakeService::class);
        $container->setDefinition(FakeService::class, $definition);

        $compilerPass = new FeatureFlagClassSwitchCompilerPass();
        $compilerPass->process($container);

        $this->assertSame(FakeService::class, $container->getDefinition(FakeService::class)->getClass());
    }

    public function testCompilerPassThrowsExceptionWhenMethodDoesNotExistInSwitchedClass(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The public methods of the original class and the switched class are not compatible.');

        $container = new ContainerBuilder();

        $featureFlagLoaderFactoryMock = $this->createMock(FeatureFlagLoaderFactory::class);
        $featureFlagLoaderInterfaceMock = $this->createMock(FeatureFlagLoaderInterface::class);
        $featureFlagLoaderInterfaceMock->method('loadFeatureFlags')
            ->willReturn([new FeatureFlag('new_feature', true)]);
        $featureFlagLoaderFactoryMock->method('create')
            ->willReturn($featureFlagLoaderInterfaceMock);

        $featureFlagProviderMock = $this->createMock(FeatureFlagProvider::class);
        $featureFlagProviderMock->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $featureFlagProviderDefinition = new Definition(FeatureFlagProvider::class);
        $featureFlagProviderDefinition->setArguments([$featureFlagLoaderFactoryMock]);

        $container->setDefinition(FeatureFlagProvider::class, $featureFlagProviderDefinition);

        $definition = new Definition(FakeService::class);
        $container->setDefinition(FakeService::class, $definition);

        $switchedDefinition = new Definition(FakeServiceSwitchNoMethod::class);
        $container->setDefinition(FakeServiceSwitchNoMethod::class, $switchedDefinition);

        $compilerPass = new FeatureFlagClassSwitchCompilerPass();
        $compilerPass->process($container);
    }

    public function testCompilerPassThrowsExceptionWhenMethodParametersAreIncompatible(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The public methods of the original class and the switched class are not compatible.');

        $container = new ContainerBuilder();

        $featureFlagLoaderFactoryMock = $this->createMock(FeatureFlagLoaderFactory::class);
        $featureFlagLoaderInterfaceMock = $this->createMock(FeatureFlagLoaderInterface::class);
        $featureFlagLoaderInterfaceMock->method('loadFeatureFlags')
            ->willReturn([new FeatureFlag('new_feature', true)]);
        $featureFlagLoaderFactoryMock->method('create')
            ->willReturn($featureFlagLoaderInterfaceMock);

        $featureFlagProviderMock = $this->createMock(FeatureFlagProvider::class);
        $featureFlagProviderMock->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $featureFlagProviderDefinition = new Definition(FeatureFlagProvider::class);
        $featureFlagProviderDefinition->setArguments([$featureFlagLoaderFactoryMock]);

        $container->setDefinition(FeatureFlagProvider::class, $featureFlagProviderDefinition);

        $definition = new Definition(FakeService::class);
        $container->setDefinition(FakeService::class, $definition);

        $switchedDefinition = new Definition(FakeServiceSwitch::class);
        $container->setDefinition(FakeServiceSwitch::class, $switchedDefinition);

        $compilerPass = new FeatureFlagClassSwitchCompilerPass();
        $compilerPass->process($container);
    }
}