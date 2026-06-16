<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\ProjectRuntime;
use Velt\Database\Migrations\Migrator;

final class MigrateRollbackCommand implements Command
{
    public function __construct(private readonly ProjectRuntime $runtime)
    {
    }

    public function name(): string
    {
        return 'migrate:rollback';
    }

    public function description(): string
    {
        return 'Rollback the last migration batch.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt migrate:rollback [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $projectPath = $this->runtime->normalizePath($input->pathOption());
        $app = $this->runtime->load($projectPath);
        $this->runtime->configureDatabase($app);

        $rolledBack = (new Migrator($this->runtime->migrationPath($app, $projectPath)))->rollback();

        if ($rolledBack === []) {
            $output->line('No migrations to rollback.');

            return 0;
        }

        foreach ($rolledBack as $migration) {
            $output->line('Rolled back: ' . $migration);
        }

        return 0;
    }
}
