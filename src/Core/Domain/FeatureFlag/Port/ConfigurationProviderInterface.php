<?php

namespace Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port;

interface ConfigurationProviderInterface
{
    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $key);
}