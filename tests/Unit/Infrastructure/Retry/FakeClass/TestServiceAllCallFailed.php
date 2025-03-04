<?php

namespace App\Tests\Unit\Infrastructure\Retry\FakeClass;

use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;

class TestServiceAllCallFailed
{
    #[Retry(attempts: 3, delay: 0)]
    public function execute(): string
    {
        throw new \RuntimeException("Toujours en échec !");
    }
}
