<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader\Decorator;

use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ApplicationLoggerInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\FeatureFlagCacheInterface;

class FeatureFlagLoaderCacheDecorator implements FeatureFlagLoaderInterface
{
    private const CACHE_KEY = 'feature_flags';

    private FeatureFlagLoaderInterface $loader;
    private ApplicationLoggerInterface $logger;
    private FeatureFlagCacheInterface $cache;

    public function __construct(
        FeatureFlagLoaderInterface $loader,
        ApplicationLoggerInterface $logger,
        FeatureFlagCacheInterface $cache
    ) {
        $this->loader = $loader;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function loadFeatureFlags(): array
    {
        $this->logger->info(sprintf('Loading feature flags from %s', get_class($this->loader)));
        $data = $this->cache->get(self::CACHE_KEY);
        if ($data) {
            $this->logger->info('Loading feature flags from cache');

            return json_decode($data, true);
        }

        $featureFlags = $this->loader->loadFeatureFlags();
        $this->logger->info(sprintf('Loaded %d feature flags.', count($featureFlags)));
        $this->cache->set(self::CACHE_KEY, json_encode($featureFlags, JSON_THROW_ON_ERROR));

        return $featureFlags;
    }
}