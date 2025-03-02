<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider;

use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Repository\FeatureFlagRepositoryInterface;

class FeatureFlagProvider
{
    private FeatureFlagRepositoryInterface $repository;

    public function __construct(FeatureFlagRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function provideState(string $flag): bool
    {
        $featureFlag = $this->repository->findByName($flag);
        if (!$featureFlag) {
            return false;
        }

        return $featureFlag->isEnabled();
    }

    /**
     * Check if all given feature flags are enabled.
     *
     * @param string[] $flags
     * @return bool
     */
    public function provideStates(array $flags): bool
    {
        $featureFlags = $this->repository->findByNames($flags);

        if (count($featureFlags) !== count($flags)) {
            return false;
        }

        foreach ($featureFlags as $featureFlag) {
            if (!$featureFlag->isEnabled()) {
                return false;
            }
        }

        return true;
    }
}