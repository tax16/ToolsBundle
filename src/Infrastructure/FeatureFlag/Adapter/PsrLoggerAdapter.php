<?php

namespace Tax16\ToolsBundle\Infrastructure\FeatureFlag\Adapter;


use Psr\Log\LoggerInterface;
use Tax16\ToolsBundle\Core\Domain\FeatureFlag\Port\ApplicationLoggerInterface;

class PsrLoggerAdapter implements ApplicationLoggerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}