<?php

namespace Tax16\ToolsBundle\Core\Domain\Retry\Attribut;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Retry
{
    public function __construct(
        public int $attempts = 3,
        public int $delay = 0,
    ) {
    }
}
