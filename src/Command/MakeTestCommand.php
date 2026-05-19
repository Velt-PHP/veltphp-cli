<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeTestCommand implements Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:test';
    }

    public function description(): string
    {
        return 'Generate a PHPUnit feature test.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt make:test <name> [--force] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->argument(0);

        if ($name === null || trim($name) === '') {
            $output->error('The test name is required.');

            return 1;
        }

        $className = Naming::suffix(Naming::pascal($name), 'Test');
        $target = $input->pathOption() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Feature' . DIRECTORY_SEPARATOR . $className . '.php';

        if ($this->filesystem->exists($target) && !$input->boolOption('force')) {
            $output->error(sprintf('Test "%s" already exists. Use --force to overwrite it.', $className));

            return 1;
        }

        $this->filesystem->write($target, $this->renderer->render('test.php.stub', [
            'CLASS_NAME' => $className,
            'NAMESPACE' => 'Tests\\Feature',
        ]));

        $output->success(sprintf('Test "%s" generated.', $className));

        return 0;
    }
}
