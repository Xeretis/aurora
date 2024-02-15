<?php

namespace Minigyima\Aurora\Concerns\Docker;

use Minigyima\Aurora\Support\ConsoleLogger;

/**
 * Trait for interacting with Docker commands
 * @package Minigyima\Aurora\Concerns
 */
trait InteractsWithDockerCommands
{
    use InteractsWithComposeFiles;

    /**
     * Generate the compose prompt for use with Docker Compose
     * @param string $command
     * @return string
     */
    private function generateComposePrompt(string $command): string
    {
        ConsoleLogger::log_info('Generating compose prompt for command: ' . $command);
        $files = [];
        $name = strtolower(config('app.name'));

        $files[] = self::getCurrentComposeFile();

        if (file_exists(base_path('docker-compose.override.yaml'))) {
            $files[] = '\"' . base_path('docker-compose.override.yaml') . '\"';
        }

        if (file_exists(base_path('docker-compose.override.yml'))) {
            $files[] = '\"' . base_path('docker-compose.override.yml') . '\"';
        }

        $profiles = [];
        if (config('aurora.sockets_enabled')) {
            $profiles[] = 'sockets';
        }

        if (config('aurora.redis_enabled')) {
            $profiles[] = 'redis';
        }

        if (config('aurora.database_enabled')) {
            $profiles[] = 'database';
        }

        if (config('aurora.queue_enabled')) {
            $profiles[] = 'queue';
        }

        if (config('aurora.scheduler_enabled')) {
            $profiles[] = 'scheduler';
        }

        $profile_str = '--profile ' . implode(' --profile ', $profiles);
        $file_str = '-f ' . implode(' -f ', $files);

        ConsoleLogger::log_trace('Using compose files: ' . implode(', ', $files), 'InteractsWithDockerCommands');
        ConsoleLogger::log_trace('Using profiles: ' . implode(', ', $profiles), 'InteractsWithDockerCommands');

        $command = "docker compose $file_str $profile_str -p $name $command";
        ConsoleLogger::log_trace('Using compose command: ' . $command, 'InteractsWithDockerCommands');

        return trim($command);
    }

    /**
     * Generate the Docker build command
     * @param string $docker_tag
     * @param string $path
     * @return string
     */
    private function generateDockerBuildCommand(string $docker_tag, string $path): string
    {
        $tag_base = explode(':', $docker_tag)[0];
        $tag_latest = $tag_base . ':latest';
        $command = 'docker build -t ' . $tag_latest . ' -t ' . $docker_tag . ' ' . $path;
        ConsoleLogger::log_trace("Docker command: $command", 'InteractsWithDockerCommands');
        return $command;
    }

    /**
     * Generate docker save command
     * @param string $docker_tag
     * @param string $path
     * @return string
     */
    private function generateDockerSaveCommand(string $docker_tag, string $path): string
    {
        $command = 'docker save -o ' . $path . ' ' . $docker_tag;
        ConsoleLogger::log_trace("Docker command: $command", 'InteractsWithDockerCommands');
        return $command;
    }
}
