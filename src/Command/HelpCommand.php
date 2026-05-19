<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Application;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

final class HelpCommand implements Command
{
    public function __construct(private readonly Application $application)
    {
    }

    public function name(): string
    {
        return 'help';
    }

    public function description(): string
    {
        return 'Display help for a Velt command.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt help <command>';
    }

    public function run(Input $input, Output $output): int
    {
        $commandName = $input->argument(0);

        if ($commandName === null) {
            $output->line($this->help());

            return 0;
        }

        $command = $this->application->command($commandName);

        if ($command === null) {
            $output->error(sprintf('Unknown command "%s".', $commandName));

            return 1;
        }

        $output->line($command->help());

        return 0;
    }
}
