<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use RuntimeException;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\ProjectRuntime;
use Velt\Database\Seeders\Seeder;
use Velt\Database\Seeders\SeederRunner;

final class DbSeedCommand implements Command
{
    public function __construct(private readonly ProjectRuntime $runtime)
    {
    }

    public function name(): string
    {
        return 'db:seed';
    }

    public function description(): string
    {
        return 'Run database seeders.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt db:seed [--class=DatabaseSeeder] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $projectPath = $this->runtime->normalizePath($input->pathOption());
        $app = $this->runtime->load($projectPath);
        $this->runtime->configureDatabase($app);

        $classes = $this->seedersToRun($projectPath, $input->option('class'));
        $runner = new SeederRunner();

        foreach ($classes as $class) {
            $seeder = $this->runtime->resolveFromContainer($app, $class);

            if (!$seeder instanceof Seeder) {
                throw new RuntimeException(sprintf('Seeder "%s" must extend %s.', $class, Seeder::class));
            }

            $runner->run($seeder);
            $output->line('Seeded: ' . $class);
        }

        if ($classes === []) {
            $output->line('No seeders to run.');
        }

        return 0;
    }

    /**
     * @return list<class-string<Seeder>>
     */
    private function seedersToRun(string $projectPath, mixed $target): array
    {
        $seedersPath = $projectPath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeders';

        if (!is_dir($seedersPath)) {
            return [];
        }

        $files = glob($seedersPath . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files, SORT_NATURAL);

        foreach ($files as $file) {
            require_once $file;
        }

        if (is_string($target) && $target !== '') {
            return $this->assertSeederClass($target);
        }

        if (class_exists('DatabaseSeeder')) {
            return $this->assertSeederClass('DatabaseSeeder');
        }

        $classes = [];
        foreach ($files as $file) {
            $class = basename($file, '.php');
            if (class_exists($class)) {
                $classes[] = $this->assertSeederClass($class)[0];
            }
        }

        return $classes;
    }

    /**
     * @return list<class-string<Seeder>>
     */
    private function assertSeederClass(string $class): array
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Seeder class "%s" was not found.', $class));
        }

        if (!is_subclass_of($class, Seeder::class)) {
            throw new RuntimeException(sprintf('Seeder "%s" must extend %s.', $class, Seeder::class));
        }

        return [$class];
    }
}
