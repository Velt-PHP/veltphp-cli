<?php

declare(strict_types=1);

namespace Velt\Cli;

use Velt\Cli\Command\Command;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Throwable;

final class Application
{
    /** @var array<string, Command> */
    private array $commands = [];

    public function __construct(
        private readonly string $name,
        private readonly string $version,
        private readonly ?Output $output = null,
    ) {
    }

    public function register(Command $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    public function run(array $argv, ?Output $output = null): int
    {
        $io = $output ?? $this->output ?? new Output();
        $input = Input::fromArgv($argv);
        $commandName = $input->command() ?? 'list';

        $command = $this->commands[$commandName] ?? null;

        if ($command === null) {
            $io->error(sprintf('Unknown command "%s".', $commandName));
            $io->line('Run "php bin/velt list" to see available commands.');

            return 1;
        }

        try {
            return $command->run($input, $io);
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return 1;
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    /**
     * @return array<string, Command>
     */
    public function commands(): array
    {
        ksort($this->commands);

        return $this->commands;
    }

    public function command(string $name): ?Command
    {
        return $this->commands[$name] ?? null;
    }
}
