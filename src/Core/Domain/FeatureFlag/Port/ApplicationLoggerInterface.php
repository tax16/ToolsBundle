<?php

namespace Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port;

interface ApplicationLoggerInterface
{
    /**
     * @param array<mixed> $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * @param array<mixed> $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param array<mixed> $context
     */
    public function warning(string $message, array $context = []): void;
}