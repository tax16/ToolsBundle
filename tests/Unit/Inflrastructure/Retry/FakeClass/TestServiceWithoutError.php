<?php

namespace App\Tests\Unit\Inflrastructure\Retry\FakeClass;

use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;

class TestServiceWithoutError
{
    #[Retry(attempts: 3, delay: 0)]
    public function execute(): string
    {
        return "Succès immédiat";
    }
}
