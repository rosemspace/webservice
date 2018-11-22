<?php

namespace Rosem\Psr\Environment;

interface EnvironmentInterface
{
    public function load(): array;

    public function get(string $key): string;

    public function getAppMode(): string;

    public function getRootDirectory(): string;

    public function getAppDirectory(): string;

    public function getMediaDirectory(): string;

    public function getTempDirectory(): string;

    public function getCacheDirectory(): string;

    public function getLogDirectory(): string;

    public function getSessionDirectory(): string;

    public function getUploadDirectory(): string;

    public function getExportDirectory(): string;

    public function isMaintenanceMode(): bool; // show maintenance page

    public function isDemoMode(): bool; // allow only partial functionality

    public function isDevelopmentMode(): bool; // show errors, no caches, show debug info

    public function isStagingMode(): bool; // show errors, use caches, no debug info

    public function isProductionMode(): bool; // hide errors, use caches, no debug info
}
