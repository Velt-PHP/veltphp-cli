<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

use RuntimeException;
use Velt\Database\DB;
use Velt\Database\DatabaseManager;
use Velt\Kernel\Application as KernelApplication;

final class ProjectRuntime
{
    public function load(string $projectPath): object
    {
        $projectPath = $this->normalizePath($projectPath);
        $bootstrap = $projectPath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';

        if (is_file($bootstrap)) {
            $loaded = require $bootstrap;
            $app = is_array($loaded) ? ($loaded['app'] ?? null) : $loaded;

            if (is_object($app)) {
                return $app;
            }
        }

        $autoload = $projectPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if (is_file($autoload)) {
            require_once $autoload;
        }

        if (!class_exists(KernelApplication::class)) {
            throw new RuntimeException('Unable to load a Velt application. Run composer install in the project first.');
        }

        return new KernelApplication($projectPath);
    }

    public function configureDatabase(object $app): void
    {
        if (!class_exists(DatabaseManager::class) || !class_exists(DB::class)) {
            throw new RuntimeException('velt/database is not installed. Add velt/database to the project dependencies.');
        }

        if (!method_exists($app, 'container')) {
            throw new RuntimeException('The loaded project application does not expose a container.');
        }

        $container = $app->container();
        $manager = $container->get(DatabaseManager::class);

        if (!$manager instanceof DatabaseManager) {
            throw new RuntimeException('The project container did not resolve a valid DatabaseManager.');
        }

        DB::setManager($manager);
    }

    public function migrationPath(object $app, string $projectPath): string
    {
        $configured = null;

        if (method_exists($app, 'config')) {
            $config = $app->config();
            $configured = $config->get('database.migrations.path', $config->get('database.migrations'));
        }

        if (is_string($configured) && $configured !== '') {
            return $this->absolutePath($configured, $projectPath);
        }

        return $projectPath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    }

    public function resolveFromContainer(object $app, string $class): object
    {
        if (method_exists($app, 'container')) {
            return $app->container()->get($class);
        }

        return new $class();
    }

    public function normalizePath(string $path): string
    {
        $real = realpath($path);

        return $real === false ? rtrim($path, DIRECTORY_SEPARATOR) : $real;
    }

    private function absolutePath(string $path, string $projectPath): string
    {
        if ($path === '') {
            return $projectPath;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1 || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return $projectPath . DIRECTORY_SEPARATOR . $path;
    }
}
