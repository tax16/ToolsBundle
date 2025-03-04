<?php

namespace Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FeatureFlagSwitchClass
{
    public function __construct(
        public string $feature,
        public string $switchedClass
    ) {}
}