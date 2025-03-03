<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use DateMalformedStringException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory\FeatureFlagLoaderFactoryInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Enum\FeatureFlagStorageType;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader\Trait\FeatureFlagLoaderTrait;

#[Autoconfigure(tags: ['feature_flag_loader'])]
class YamlFeatureFlagLoader implements FeatureFlagLoaderFactoryInterface
{
    use FeatureFlagLoaderTrait;
    private string $yamlPath;

    public function __construct(
        #[Autowire(param: 'feature_flags.storage.path')]
        string $yamlPath
    ) {
        $this->yamlPath = $yamlPath;
    }

    /**
     * @inheritDoc
     * @throws DateMalformedStringException
     */
    public function loadFeatureFlags(): array
    {
        $data = Yaml::parseFile($this->yamlPath);

        return $this->parseFeatureFlags($data);
    }

    public function supports(string $storageType): bool
    {
        return $storageType === FeatureFlagStorageType::YAML->value;
    }
}