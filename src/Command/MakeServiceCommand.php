<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Command\Concerns\WritesGeneratedFiles;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeServiceCommand implements Command
{
    use WritesGeneratedFiles;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:service';
    }

    public function description(): string
    {
        return 'Generate a skeleton-compatible service.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt make:service <name> [--force] [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->argument(0);

        if ($name === null || trim($name) === '') {
            $output->error('The service name is required.');

            return 1;
        }

        $classBase = Naming::pascal($name);
        $className = Naming::suffix($classBase, 'Service');
        $featureName = str_ends_with($classBase, 'Service') ? substr($classBase, 0, -7) : $classBase;
        $target = $input->pathOption() . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . $featureName . DIRECTORY_SEPARATOR . $className . '.php';

        if (!$this->writeGeneratedFile($this->filesystem, $input, $output, $target, $this->renderer->render('service.php.stub', [
            'CLASS_NAME' => $className,
            'NAMESPACE' => 'App\\' . $featureName,
        ]), sprintf('Service "%s"', $className))) {
            return 1;
        }

        $output->success(sprintf('Service "%s" generated.', $className));

        return 0;
    }
}
