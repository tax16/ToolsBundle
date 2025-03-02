<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use DateTime;
use Symfony\Component\Yaml\Yaml;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Loader\FeatureFlagLoaderInterface;

class JsonFeatureFlagLoader implements FeatureFlagLoaderInterface
{
    private string $yamlPath;

    public function __construct(string $yamlPath)
    {
        $this->yamlPath = $yamlPath;
    }

    /**
     * @inheritDoc
     */
    public function loadFeatureFlags(): array
    {
        $data = Yaml::parseFile($this->yamlPath);

        return $this->parseFeatureFlags($data);
    }

    private function parseFeatureFlags(array $data): array
    {
        $featureFlags = [];
        foreach ($data as $flagData) {
            $startDate = !isset($flagData['start_date']) ? null : new DateTime($flagData['start_date']);
            $endDate = !isset($flagData['end_date']) ? null : new DateTime($flagData['end_date']);

            $featureFlags[] = new FeatureFlag(
                $flagData['name'],
                $flagData['enabled'],
                $startDate,
                $endDate
            );
        }

        return $featureFlags;
    }

    public function supports(string $storageType): bool
    {
        return $storageType === 'doctrine';
    }
}