<?php

namespace Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port;

interface FeatureFlagCacheInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $ttl = 3600): void;

    public function delete(string $key): void;

    public function clear(): void;
}