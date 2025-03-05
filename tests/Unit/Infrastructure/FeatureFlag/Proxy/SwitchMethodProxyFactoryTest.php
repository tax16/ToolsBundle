<?php

namespace App\Tests\Unit\Infrastructure\FeatureFlag\Proxy;

use App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass\FakeClassOne;
use App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass\FakeClassSwitchMethod;
use App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass\FakeClassSwitchMethodParamDiff;
use App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass\FakeClassSwitchMethodWithMissingAlternative;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider\FeatureFlagProvider;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ApplicationLoggerInterface;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Proxy\SwitchMethodProxyFactory;

class SwitchMethodProxyFactoryTest extends TestCase
{
    private ApplicationLoggerInterface $logger;
    private FeatureFlagProvider $featureFlagProvider;
    private SwitchMethodProxyFactory $proxyFactory;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(ApplicationLoggerInterface::class);
        $this->featureFlagProvider = $this->createMock(FeatureFlagProvider::class);

        $this->proxyFactory = new SwitchMethodProxyFactory(
            $this->logger,
            $this->featureFlagProvider
        );
    }

    public function testExecuteMethodWithoutAttributeCallsOriginalMethod(): void
    {
        $proxy = $this->proxyFactory->createProxy(new FakeClassOne());
        $result = $proxy->execute();

        $this->assertSame("Original Method", $result);
    }

    public function testExecuteMethodSwitchesWhenFeatureFlagIsActive(): void
    {
        $service = new FakeClassSwitchMethod();

        $this->featureFlagProvider
            ->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $this->logger
            ->expects($this->once())
            ->method('info');

        $proxy = $this->proxyFactory->createProxy($service);
        $result = $proxy->execute();
        $this->assertSame("Switched Method", $result);
    }

    public function testExecuteMethodDoesNotSwitchWhenFeatureFlagIsInactive(): void
    {
        $service = new FakeClassSwitchMethod();

        $this->featureFlagProvider
            ->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(false);

        $this->logger
            ->expects($this->never())
            ->method('info');

        $proxy = $this->proxyFactory->createProxy($service);
        $result = $proxy->execute();

        $this->assertSame("Original Method", $result);
    }

    public function testExecuteMethodThrowsExceptionWhenParametersAreIncompatible(): void
    {
        $service = new FakeClassSwitchMethodParamDiff();

        $this->featureFlagProvider
            ->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $this->logger
            ->expects($this->once())
            ->method('info');

        /** @var FakeClassSwitchMethodParamDiff $proxy */
        $proxy = $this->proxyFactory->createProxy($service);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The parameters of the method 'execute' and the alternative method 'alternativeMethod' are not compatible.");

        $proxy->execute();
    }

    public function testExecuteMethodThrowsExceptionWhenAlternativeMethodDoesNotExist(): void
    {
        $service = new FakeClassSwitchMethodWithMissingAlternative();

        $this->featureFlagProvider
            ->method('provideStateByFlag')
            ->with('new_feature')
            ->willReturn(true);

        $this->logger
            ->expects($this->once())
            ->method('info');

        /** @var FakeClassSwitchMethodWithMissingAlternative $proxy */
        $proxy = $this->proxyFactory->createProxy($service);

        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage("Method App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass\FakeClassSwitchMethodWithMissingAlternative::alternativeMethod() does not exist");

        $proxy->execute();
    }
}