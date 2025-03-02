<?php

namespace App\Tests\Unit\Inflrastructure\Retry\FakeClass;

use Exception;
use Tax16\ToolsBundle\Core\Domain\Attribut\Retry\Retry;

class TestServiceWithoutError
{
    #[Retry(attempts: 3, delay: 0)]
    public function execute(): string
    {
        return "Succès immédiat";
    }
}
