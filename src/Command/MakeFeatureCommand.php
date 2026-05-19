<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use RuntimeException;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

final class MakeFeatureCommand implements Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function name(): string
    {
        return 'make:feature';
    }

    public function description(): string
    {
        return 'Generate a feature-based Velt module.';
    }

    public function help(): string
    {
        return <<<'HELP'
Usage: php bin/velt make:feature <name> [--force] [--path=/project] [--no-interaction]

Generates:
  features/<name>/<ClassName>Controller.php
  features/<name>/<ClassName>Service.php
  features/<name>/<ClassName>Model.php
  features/<name>/views/login.velt.php
  features/<name>/tests/<ClassName>ControllerTest.php
HELP;
    }

    public function run(Input $input, Output $output): int
    {
        $feature = $input->argument(0);

        if ($feature === null || trim($feature) === '') {
            $output->error('The feature name is required.');

            return 1;
        }

        $featureName = Naming::kebab($feature);
        $className = Naming::pascal($feature);
        $basePath = $input->pathOption();
        $featurePath = $basePath . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . $featureName;
        $force = $input->boolOption('force');

        if ($this->filesystem->exists($featurePath) && !$force) {
            $output->error(sprintf('Feature "%s" already exists. Use --force to overwrite it.', $featureName));

            return 1;
        }

        $variables = [
            'FEATURE_NAME' => $featureName,
            'CLASS_NAME' => $className,
            'NAMESPACE' => 'App\\Features\\' . $className,
        ];

        $files = [
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Controller.php' => 'feature/Controller.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Service.php' => 'feature/Service.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Model.php' => 'feature/Model.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'login.velt.php' => 'feature/login.velt.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $className . 'ControllerTest.php' => 'feature/ControllerTest.php.stub',
        ];

        foreach ($files as $target => $template) {
            if ($this->filesystem->exists($target) && !$force) {
                throw new RuntimeException(sprintf('File already exists: %s', $target));
            }

            $this->filesystem->write($target, $this->renderer->render($template, $variables));
        }

        $output->success(sprintf('Feature "%s" generated.', $featureName));

        return 0;
    }
}
