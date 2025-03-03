<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Adapter;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ConfigurationProviderInterface;

class ParameterBagAdapter implements ConfigurationProviderInterface
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @param ParameterBagInterface $parameterSource
     */
    public function __construct(ParameterBagInterface $parameterSource)
    {
        $this->parameters = $parameterSource;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->parameters->get($key);
    }
}