<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider;

interface FeatureFlagProviderInterface
{
    public function provideStateByFlag(string $flag): bool;
}