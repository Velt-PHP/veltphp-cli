<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeModelCommand implements Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:model';
    }

    public function description(): string
    {
        return 'Generate a Velt model.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt make:model <name> [--force] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->argument(0);

        if ($name === null || trim($name) === '') {
            $output->error('The model name is required.');

            return 1;
        }

        $className = Naming::pascal($name);
        $target = $input->pathOption() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $className . '.php';

        if ($this->filesystem->exists($target) && !$input->boolOption('force')) {
            $output->error(sprintf('Model "%s" already exists. Use --force to overwrite it.', $className));

            return 1;
        }

        $this->filesystem->write($target, $this->renderer->render('model.php.stub', [
            'CLASS_NAME' => $className,
            'NAMESPACE' => 'App\\Models',
        ]));

        $output->success(sprintf('Model "%s" generated.', $className));

        return 0;
    }
}
