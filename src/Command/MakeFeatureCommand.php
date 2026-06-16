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
  features/<ClassName>/<ClassName>Page.php
  features/<ClassName>/<ClassName>Controller.php
  features/<ClassName>/<ClassName>Service.php
  features/<ClassName>/<ClassName>Model.php
  features/<ClassName>/Tests/<ClassName>ControllerTest.php
HELP;
    }

    public function run(Input $input, Output $output): int
    {
        $feature = $input->argument(0);

        if ($feature === null || trim($feature) === '') {
            $output->error('The feature name is required.');

            return 1;
        }

        $className = Naming::pascal($feature);
        $featureName = $className;
        $tableName = str_replace('-', '_', Naming::kebab($feature));
        $basePath = $input->pathOption();
        $featurePath = $basePath . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . $featureName;
        $force = $input->boolOption('force');

        if ($this->filesystem->exists($featurePath) && !$force) {
            $output->error(sprintf('Feature "%s" already exists. Use --force to overwrite it.', $featureName));

            return 1;
        }

        $variables = [
            'FEATURE_NAME' => $tableName,
            'CLASS_NAME' => $className,
            'NAMESPACE' => 'App\\' . $className,
        ];

        $files = [
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Page.php' => 'feature/Page.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Controller.php' => 'feature/Controller.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Service.php' => 'feature/Service.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . $className . 'Model.php' => 'feature/Model.php.stub',
            $featurePath . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . $className . 'ControllerTest.php' => 'feature/ControllerTest.php.stub',
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
