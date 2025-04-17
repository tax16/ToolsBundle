<?php

namespace App\Tests\Unit\Infrastructure\Retry\FakeClass;

use Tax16\ToolsBundle\Core\Domain\Retry\Attribut\Retry;

class TestServiceFirstCallFailedButNextSuccess
{
    private int $callCount = 0;

    #[Retry(attempts: 3, delay: 0)]
    public function execute(): string
    {
        $this->callCount++;
        if ($this->callCount < 2) {
            throw new \RuntimeException("Première tentative échouée !");
        }

        return "Succès après échec";
    }
}
