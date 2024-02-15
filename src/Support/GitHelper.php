<?php

namespace Minigyima\Aurora\Support;

use Symfony\Component\Process\Process;

/**
 * Helper class for git operations
 * @package Minigyima\Aurora\Support
 * @internal
 */
class GitHelper
{
    /**
     * Check if a given path is a git repository
     * @param string $repo_path
     * @return bool
     */
    public static function isRepo(string $repo_path): bool
    {
        $process = Process::fromShellCommandline("git -C \"$repo_path\" rev-parse");
        return 0 === $process->run();
    }

    /**
     * Check if a given git repository is dirty
     * @param string $repo_path
     * @return bool
     */
    public static function isDirty(string $repo_path): bool
    {
        return '' != shell_exec("git -C \"$repo_path\" status --porcelain");
    }

    /**
     * Get the list of files ignored by git
     * @param string $repo_path
     * @return array
     */
    public static function getIgnoredFiles(string $repo_path): array
    {
        return array_map(fn($item) => rtrim($item, DIRECTORY_SEPARATOR),
            explode(
                PHP_EOL,
                trim(shell_exec("git -C \"$repo_path\" ls-files --exclude-standard -oi --directory"))
            ));
    }

    /**
     * Initialize a git repository
     * @param string $repo_path
     * @return bool
     */
    public static function init(string $repo_path): bool
    {
        return 0 === (int) shell_exec("git -C \"$repo_path\" init");
    }

    /**
     * Get the current git branch
     * @param string $repo_path
     * @return string
     */
    public static function getCurrentBranch(string $repo_path): string
    {
        return trim(shell_exec("git -C \"$repo_path\" rev-parse --abbrev-ref HEAD"));
    }

    /**
     * Get the current git commit hash
     * @param string $repo_path
     * @return string
     */
    public static function getCurrentCommitHash(string $repo_path): string
    {
        return trim(shell_exec("git -C \"$repo_path\" rev-parse HEAD"));
    }
}
