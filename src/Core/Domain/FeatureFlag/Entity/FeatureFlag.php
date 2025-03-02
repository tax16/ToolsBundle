<?php

namespace Tax16\ToolsBundle\Core\Domain\FeatureFlag\Entity;

use DateTime;
use DateTimeInterface;

class FeatureFlag
{
    private string $name;
    private bool $enabled;
    private ?DateTimeInterface $startDate;
    private ?DateTimeInterface $endDate;

    public function __construct(
        string $name,
        bool $enabled,
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ) {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function update(
        ?bool $enabled = null,
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ): void {
        if ($enabled !== null) {
            $this->enabled = $enabled;
        }

        if ($startDate !== null) {
            $this->startDate = $startDate;
        }

        if ($endDate !== null) {
            $this->endDate = $endDate;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $now = new DateTime();

        if ($this->endDate && $now > $this->endDate) {
            return false;
        }

        if ($this->startDate && $now < $this->startDate) {
            return false;
        }

        return true;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }
}