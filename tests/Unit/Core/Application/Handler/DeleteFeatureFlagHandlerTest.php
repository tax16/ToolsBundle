<?php

namespace App\Tests\Unit\Core\Application\Handler;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Handler\DeleteFeatureFlagHandler;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Repository\FeatureFlagRepositoryInterface;

class DeleteFeatureFlagHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(FeatureFlagRepositoryInterface::class);

        $this->handler = new DeleteFeatureFlagHandler($this->repositoryMock);
    }

    public function testHandleDeletesFeatureFlagWhenExists()
    {
        $flagName = 'test_flag';

        $featureFlagMock = $this->createMock(FeatureFlag::class);

        $this->repositoryMock->method('findByName')->with($flagName)->willReturn($featureFlagMock);

        $this->repositoryMock->expects($this->once())->method('delete')->with($featureFlagMock);

        $this->handler->handle($flagName);
    }

    public function testHandleThrowsExceptionWhenFeatureFlagNotFound()
    {
        $flagName = 'unknown_flag';

        $this->repositoryMock->method('findByName')->with($flagName)->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Feature flag '$flagName' not found.");

        // Exécution du handler
        $this->handler->handle($flagName);
    }
}