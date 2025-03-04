<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Provider;

use Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory\FeatureFlagLoaderFactory;

class FeatureFlagProvider implements FeatureFlagProviderInterface
{
    private FeatureFlagLoaderFactory $featureFlagLoaderFactory;

    public function __construct(FeatureFlagLoaderFactory $featureFlagLoaderFactory)
    {
        $this->featureFlagLoaderFactory = $featureFlagLoaderFactory;
    }

    /**
     *
     * @param string $flag
     * @return bool
     */
    public function provideStateByFlag(string $flag): bool
    {
        $loader = $this->featureFlagLoaderFactory->create();
        $featureFlags = $loader->loadFeatureFlags();

        $flag = mb_strtolower($flag);

        $filteredFlags = array_filter($featureFlags, static fn($featureFlag) => mb_strtolower($featureFlag->getName()) === $flag);

        if (empty($filteredFlags)) {
            throw new \InvalidArgumentException(sprintf('Feature flag "%s" does not exist.', $flag));
        }

        return array_values($filteredFlags)[0]->isEnabled();
    }

    /**
     * Check if all given feature flags are enabled.
     *
     * @param string[] $flags
     * @return bool
     */
    public function provideStateByFlags(array $flags): bool
    {
        $loader = $this->featureFlagLoaderFactory->create();
        $featureFlags = $loader->loadFeatureFlags();

        $flags = array_map('mb_strtolower', $flags);

        $filteredFlags = array_filter($featureFlags, static fn($featureFlag) => in_array(mb_strtolower($featureFlag->getName()), $flags, true));

        $foundFlagNames = array_map(static fn($featureFlag) => mb_strtolower($featureFlag->getName()), $filteredFlags);

        $missingFlags = array_diff($flags, $foundFlagNames);

        if (!empty($missingFlags)) {
            throw new \InvalidArgumentException(sprintf('Some feature flags do not exist: %s', implode(', ', $missingFlags)));
        }

        return array_reduce($filteredFlags, static fn($carry, $featureFlag) => $carry && $featureFlag->isEnabled(), true);
    }
}