<?php

namespace Minigyima\Aurora\Concerns;

use Minigyima\Aurora\Config\Constants;

/**
 * EnsuresAuroraStorageExists - Trait for ensuring that the storage directory exists
 * @package Minigyima\Aurora\Concerns
 */
trait EnsuresAuroraStorageExists
{
    /**
     * Ensure the storage directory exists
     * @return void
     */
    private function ensureStorageExists(): void
    {
        if (! file_exists(base_path(Constants::AURORA_DOCKER_STORAGE_PATH))) {
            mkdir(base_path(Constants::AURORA_DOCKER_STORAGE_PATH), 0777, true);
        }

        if (! file_exists(base_path(Constants::AURORA_DOCKER_STORAGE_PATH)) . '/.gitignore') {
            file_put_contents(base_path(Constants::AURORA_DOCKER_STORAGE_PATH) . '/.gitignore', '*');
        }

        if (! file_exists(base_path(Constants::AURORA_DOCKER_STORAGE_PATH) . '/logs/nginx')) {
            mkdir(base_path(Constants::AURORA_DOCKER_STORAGE_PATH) . '/logs/nginx', 0777, true);
        }
    }
}
