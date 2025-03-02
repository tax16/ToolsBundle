<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;

class FeatureFlagLoaderFactory
{
    private array $loaders;
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters, iterable $loaders)
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


    public function supports(string $storageType): bool
    {
        return false;
    }
}