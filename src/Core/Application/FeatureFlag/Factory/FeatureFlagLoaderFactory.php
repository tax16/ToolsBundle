<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory;

use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ConfigurationProviderInterface;

class FeatureFlagLoaderFactory
{
    /**
     * @var array<FeatureFlagLoaderInterface>
     */
    private array $loaders;
    private ConfigurationProviderInterface $parameters;

    public function __construct(ConfigurationProviderInterface $parameters, iterable $loaders)
    {
        $this->parameters = $parameters;
        $this->loaders = iterator_to_array($loaders);
    }

    public function create(): FeatureFlagLoaderInterface
    {
        $storageType = $this->parameters->get('feature_flags.storage.type');

        foreach ($this->loaders as $loader) {
            if ($loader->supports($storageType)) {
                return $loader;
            }
        }

        throw new \InvalidArgumentException("No loader found for the given storage type: $storageType");
    }
}