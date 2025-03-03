<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader;

use JsonException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Tax16\ToolsBundle\Core\Application\FeatureFlag\Factory\FeatureFlagLoaderFactoryInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Enum\FeatureFlagStorageType;
use Tax16\ToolsBundle\Infrastructure\FeatureFlag\Loader\Trait\FeatureFlagLoaderTrait;

#[Autoconfigure(tags: ['feature_flag_loader'])]
class JsonFeatureFlagLoader implements FeatureFlagLoaderFactoryInterface
{
    use FeatureFlagLoaderTrait;

    private string $jsonPath;

    public function __construct(
        #[Autowire(param: 'feature_flags.storage.path')]
        string $yamlPath
    )
    {
        $this->jsonPath = $yamlPath;
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function loadFeatureFlags(): array
    {
        $data = json_decode(file_get_contents($this->jsonPath), true, 512, JSON_THROW_ON_ERROR);

        return $this->parseFeatureFlags($data);
    }

    public function supports(string $storageType): bool
    {
        return $storageType === FeatureFlagStorageType::JSON->value;
    }
}