<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;

class DoctrineFeatureFlagLoader implements FeatureFlagLoaderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function loadFeatureFlags(): array
    {
        return $this->entityManager->getRepository(FeatureFlag::class)->findAll();
    }


    public function supports(string $storageType): bool
    {
        return $storageType === 'doctrine';
    }
}