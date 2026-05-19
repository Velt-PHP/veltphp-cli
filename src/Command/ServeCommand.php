<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

final class ServeCommand implements Command
{
    public function name(): string
    {
        return 'serve';
    }

    public function description(): string
    {
        return 'Start the local PHP development server.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt serve [--host=127.0.0.1] [--port=8000] [--path=/project] [--dry-run]';
    }

    public function run(Input $input, Output $output): int
    {
        $host = (string) $input->option('host', '127.0.0.1');
        $port = (string) $input->option('port', '8000');
        $publicPath = $input->pathOption() . DIRECTORY_SEPARATOR . 'public';

        if (!is_dir($publicPath)) {
            $output->error(sprintf('The public directory does not exist: %s', $publicPath));

            return 1;
        }

        $command = sprintf(
            '%s -S %s:%s -t %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($publicPath),
        );

        $output->line(sprintf('Velt development server available at http://%s:%s', $host, $port));
        $output->line(sprintf('Command: %s', $command));

        if ($input->boolOption('dry-run')) {
            return 0;
        }

        passthru($command, $exitCode);

        return $exitCode === 0 ? 0 : 1;
    }
}
