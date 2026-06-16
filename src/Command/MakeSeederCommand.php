<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Command\Concerns\WritesGeneratedFiles;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeSeederCommand implements Command
{
    use WritesGeneratedFiles;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:seeder';
    }

    public function description(): string
    {
        return 'Generate a Velt database seeder.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt make:seeder <name> [--force] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->argument(0);

        if ($name === null || trim($name) === '') {
            $output->error('The seeder name is required.');

            return 1;
        }

        $className = Naming::suffix(Naming::pascal($name), 'Seeder');
        $target = $input->pathOption() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeders' . DIRECTORY_SEPARATOR . $className . '.php';

        if (!$this->writeGeneratedFile($this->filesystem, $input, $output, $target, $this->renderer->render('seeder.php.stub', [
            'CLASS_NAME' => $className,
        ]), sprintf('Seeder "%s"', $className))) {
            return 1;
        }

        $output->success(sprintf('Seeder "%s" generated.', $className));

        return 0;
    }
}
