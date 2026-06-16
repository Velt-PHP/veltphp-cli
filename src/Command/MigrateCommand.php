<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\ProjectRuntime;
use Velt\Database\Migrations\Migrator;

final class MigrateCommand implements Command
{
    public function __construct(private readonly ProjectRuntime $runtime)
    {
    }

    public function name(): string
    {
        return 'migrate';
    }

    public function description(): string
    {
        return 'Run pending database migrations.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt migrate [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $projectPath = $this->runtime->normalizePath($input->pathOption());
        $app = $this->runtime->load($projectPath);
        $this->runtime->configureDatabase($app);

        $executed = (new Migrator($this->runtime->migrationPath($app, $projectPath)))->migrate();

        if ($executed === []) {
            $output->line('No migrations to run.');

            return 0;
        }

        foreach ($executed as $migration) {
            $output->line('Migrated: ' . $migration);
        }

        return 0;
    }
}
