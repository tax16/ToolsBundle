<?php

namespace App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass;

use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchMethod;

class FakeClassSwitchMethodParamDiff
{
    #[FeatureFlagSwitchMethod(feature: 'new_feature', method: 'alternativeMethod')]
    public function execute(): string
    {
        return "Original Method";
    }

    public function alternativeMethod(int $fake): string
    {
        return "Switched Method";
    }
}