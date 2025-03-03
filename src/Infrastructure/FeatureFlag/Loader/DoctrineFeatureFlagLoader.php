<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory\FeatureFlagLoaderFactoryInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Enum\FeatureFlagStorageType;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Persistence\DoctrineFeatureFlagRepository;

#[Autoconfigure(tags: ['feature_flag_loader'])]
class DoctrineFeatureFlagLoader implements FeatureFlagLoaderFactoryInterface
{
    private DoctrineFeatureFlagRepository $featureFlagRepository;

    public function __construct(DoctrineFeatureFlagRepository $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * @inheritDoc
     */
    public function loadFeatureFlags(): array
    {
        return $this->featureFlagRepository->findAll();
    }


    public function supports(string $storageType): bool
    {
        return $storageType === FeatureFlagStorageType::DOCTRINE->value;
    }
}