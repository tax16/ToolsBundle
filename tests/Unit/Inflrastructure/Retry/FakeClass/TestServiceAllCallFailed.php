<?php

namespace App\Tests\Unit\Inflrastructure\Retry\FakeClass;

use Tax16\ToolsBundle\Core\Domain\Attribut\Retry\Retry;

class TestServiceAllCallFailed
{
    #[Retry(attempts: 3, delay: 0)]
    public function execute(): string
    {
        throw new \RuntimeException("Toujours en échec !");
    }
}
