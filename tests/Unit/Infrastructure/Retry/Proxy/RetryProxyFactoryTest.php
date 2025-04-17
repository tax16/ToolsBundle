<?php

namespace App\Tests\Unit\Infrastructure\Retry\Proxy;

use App\Tests\Unit\Infrastructure\Retry\FakeClass\TestServiceAllCallFailed;
use App\Tests\Unit\Infrastructure\Retry\FakeClass\TestServiceFirstCallFailedButNextSuccess;
use App\Tests\Unit\Infrastructure\Retry\FakeClass\TestServiceWithoutError;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tax16\ToolsBundle\Infrastructure\Retry\Proxy\RetryProxyFactory;

class RetryProxyFactoryTest extends TestCase
{
    private RetryProxyFactory $proxyFactory;

    protected function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->any())->method('warning');

        $this->proxyFactory = new RetryProxyFactory($loggerMock);
    }

    public function testFirstCallFailsButNextSucceeds(): void
    {
        $service = new TestServiceFirstCallFailedButNextSuccess();
        $proxy = $this->proxyFactory->createProxy($service);

        $result = $proxy->execute();

        $this->assertEquals("Succès après échec", $result);
    }

    public function testAllCallsFail(): void
    {
        $service = new TestServiceAllCallFailed();
        $proxy = $this->proxyFactory->createProxy($service);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Toujours en échec !");

        $proxy->execute();
    }

    public function testWithoutError(): void
    {
        $service = new TestServiceWithoutError();
        $proxy = $this->proxyFactory->createProxy($service);

        $result = $proxy->execute();

        $this->assertEquals("Succès immédiat", $result);
    }
}
