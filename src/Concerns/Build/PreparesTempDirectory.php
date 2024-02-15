<?php

namespace Minigyima\Aurora\Concerns\Build;

use Minigyima\Aurora\Config\Constants;
use Minigyima\Aurora\Support\ConsoleLogger;
use Minigyima\Aurora\Support\GitHelper;
use function Minigyima\Aurora\Support\rrmdir;
use function Minigyima\Aurora\Support\rsync;
use function Minigyima\Aurora\Support\rsync_repo_ignore;

/**
 * PreparesTempDirectory - Prepares the temporary directory for the build process
 * @package Minigyima\Aurora\Concerns\Build
 * @internal
 */
trait PreparesTempDirectory
{
    /**
     * Prepare the temporary directory for the build process
     * @return void
     * @internal
     */
    private function prepareTempDirectory(): void
    {
        ConsoleLogger::log_info('Preparing temporary directory...', 'PrepareTempDirectory');
        $this->rmTemp();
        $this->copyAssets();
        $this->copySource();
        ConsoleLogger::log_trace('Writing current commit hash to temporary directory...', 'PrepareTempDirectory');
        file_put_contents($this->tempPath() . '/assets/git_commit', GitHelper::getCurrentCommitHash(base_path()));
        ConsoleLogger::log_success('Temporary directory prepared', 'PrepareTempDirectory');
    }

    /**
     * Remove the temporary directory
     * @return void
     * @internal
     */
    private function rmTemp(): void
    {
        if (file_exists($this->tempPath())) {
            ConsoleLogger::log_warning('Cleaning up temporary directory...', 'PrepareTempDirectory');
            rrmdir($this->tempPath());
        }
        mkdir($this->tempPath());
    }

    /**
     * Get the path to the temporary directory
     * @return string
     * @internal
     */
    private function tempPath(): string
    {
        return '"' . base_path(Constants::AURORA_TEMP_PATH) . '"';
    }

    /**
     * Copy assets to the temporary directory
     * @return void
     * @internal
     */
    private function copyAssets(): void
    {
        if ($this->usePublishedAssets()) {
            ConsoleLogger::log_warning('Using published assets', 'PrepareTempDirectory');
            rsync(base_path('docker/prod'), $this->tempPath() . '/assets');
            return;
        }

        ConsoleLogger::log_trace('Copying assets to temporary directory...', 'PrepareTempDirectory');
        mkdir($this->tempPath() . '/assets');
        rsync(__DIR__ . '/../../Stubs/docker/prod', $this->tempPath() . '/assets');
    }

    /**
     * Use published assets
     * @return bool
     * @internal
     */
    private function usePublishedAssets(): bool
    {
        return file_exists(base_path('docker/prod'));
    }

    /**
     * Copy source code to the temporary directory
     * @return void
     * @internal
     */
    private function copySource(): void
    {
        ConsoleLogger::log_trace('Copying source code to temporary directory...', 'PrepareTempDirectory');
        mkdir($this->tempPath() . '/source');
        rsync_repo_ignore(base_path(), $this->tempPath() . '/source');
    }
}
