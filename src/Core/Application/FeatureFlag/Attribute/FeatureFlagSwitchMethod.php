<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class FeatureFlagSwitchMethod
{
    public function __construct(
        public string $feature,
        public string $method
    ) {}
}