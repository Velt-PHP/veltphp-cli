<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Application;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

final class ListCommand implements Command
{
    public function __construct(private readonly Application $application)
    {
    }

    public function name(): string
    {
        return 'list';
    }

    public function description(): string
    {
        return 'Display available Velt commands.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt list';
    }

    public function run(Input $input, Output $output): int
    {
        $output->line(sprintf('%s %s', $this->application->name(), $this->application->version()));
        $output->line('');
        $output->line('Available commands:');

        foreach ($this->application->commands() as $command) {
            $output->line(sprintf('  %-16s %s', $command->name(), $command->description()));
        }

        return 0;
    }
}
