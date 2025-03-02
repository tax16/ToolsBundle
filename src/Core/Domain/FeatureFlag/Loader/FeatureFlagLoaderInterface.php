<?php

namespace Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader;

use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;

interface FeatureFlagLoaderInterface
{
    /**
     * Charge les Feature Flags.
     *
     * @return FeatureFlag[]
     */
    public function loadFeatureFlags(): array;
}