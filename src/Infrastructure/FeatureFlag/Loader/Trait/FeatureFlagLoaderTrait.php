<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader\Trait;

use DateTime;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity\FeatureFlag;

trait FeatureFlagLoaderTrait
{
    protected function parseFeatureFlags(array $data): array
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
}