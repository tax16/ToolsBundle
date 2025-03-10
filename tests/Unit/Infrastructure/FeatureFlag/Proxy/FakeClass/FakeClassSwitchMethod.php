<?php

namespace App\Tests\Unit\Infrastructure\FeatureFlag\Proxy\FakeClass;

use Tax16\ToolsBundle\Core\Application\FeatureFlag\Attribute\FeatureFlagSwitchMethod;

class FakeClassSwitchMethod
{
    #[FeatureFlagSwitchMethod(feature: 'new_feature', method: 'alternativeMethod')]
    public function execute(): string
    {
        return "Original Method";
    }

    public function alternativeMethod(): string
    {
        return "Switched Method";
    }
}