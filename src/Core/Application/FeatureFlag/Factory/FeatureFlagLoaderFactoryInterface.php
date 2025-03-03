<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory;

use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;

interface FeatureFlagLoaderFactoryInterface extends FeatureFlagLoaderInterface
{
    public function supports(string $storageType): bool;
}