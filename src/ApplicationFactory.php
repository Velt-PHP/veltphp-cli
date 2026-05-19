<?php

declare(strict_types=1);

namespace Velt\Cli;

use Velt\Cli\Command\HelpCommand;
use Velt\Cli\Command\ListCommand;
use Velt\Cli\Command\MakeControllerCommand;
use Velt\Cli\Command\MakeFeatureCommand;
use Velt\Cli\Command\MakeModelCommand;
use Velt\Cli\Command\MakeTestCommand;
use Velt\Cli\Command\ServeCommand;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\TemplateRenderer;

final class ApplicationFactory
{
    public static function create(): Application
    {
        $templatesPath = dirname(__DIR__) . '/resources/templates';
        $filesystem = new Filesystem();
        $renderer = new TemplateRenderer($templatesPath);

        $application = new Application('Velt CLI', '0.1.0');
        $application->register(new ListCommand($application));
        $application->register(new HelpCommand($application));
        $application->register(new MakeFeatureCommand($filesystem, $renderer));
        $application->register(new MakeControllerCommand($filesystem, $renderer));
        $application->register(new MakeModelCommand($filesystem, $renderer));
        $application->register(new MakeTestCommand($filesystem, $renderer));
        $application->register(new ServeCommand());

        return $application;
    }
}
