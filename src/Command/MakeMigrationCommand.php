<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Command\Concerns\WritesGeneratedFiles;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeMigrationCommand implements Command
{
    use WritesGeneratedFiles;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:migration';
    }

    public function description(): string
    {
        return 'Generate a Velt database migration.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt make:migration <name> [--force] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->argument(0);

        if ($name === null || trim($name) === '') {
            $output->error('The migration name is required.');

            return 1;
        }

        $normalized = str_replace('-', '_', Naming::kebab($name));
        $directory = $input->pathOption() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';

        if (!$input->boolOption('force') && (glob($directory . DIRECTORY_SEPARATOR . '*_' . $normalized . '.php') ?: []) !== []) {
            $output->error(sprintf('Migration "%s" already exists. Use --force to overwrite it.', $normalized));

            return 1;
        }

        $target = $directory . DIRECTORY_SEPARATOR . date('Y_m_d_His') . '_' . $normalized . '.php';

        if (!$this->writeGeneratedFile($this->filesystem, $input, $output, $target, $this->renderer->render('migration.php.stub', [
            'TABLE_NAME' => $this->tableName($normalized),
        ]), sprintf('Migration "%s"', basename($target)))) {
            return 1;
        }

        $output->success(sprintf('Migration "%s" generated.', basename($target)));

        return 0;
    }

    private function tableName(string $name): string
    {
        $name = preg_replace('/^(create|add|update|drop)_/', '', $name) ?? $name;
        $name = preg_replace('/_table$/', '', $name) ?? $name;

        return $name === '' ? 'items' : $name;
    }
}
