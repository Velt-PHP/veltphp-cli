<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Velt\Kernel\Application as KernelApplication;
use Velt\Kernel\Contracts\ApplicationInterface;
use Velt\Kernel\Contracts\ContainerInterface;

final class KernelCheckCommand implements Command
{
    public function name(): string
    {
        return 'kernel:check';
    }

    public function description(): string
    {
        return 'Verify that the Velt kernel answers through its public contracts.';
    }

    public function help(): string
    {
        return 'Usage: php bin/velt kernel:check [--path=/project]';
    }

    public function run(Input $input, Output $output): int
    {
        $kernel = new KernelApplication($input->pathOption());

        if (! $kernel instanceof ApplicationInterface) {
            $output->error('Kernel application does not implement ApplicationInterface.');

            return 1;
        }

        $container = $kernel->container();

        if (! $container instanceof ContainerInterface) {
            $output->error('Kernel container does not implement ContainerInterface.');

            return 1;
        }

        if (! $container->has('app') || $container->get('app') !== $kernel) {
            $output->error('Kernel container did not expose the application binding.');

            return 1;
        }

        $kernel->boot();

        $output->success('Kernel contracts responded successfully.');
        $output->line(sprintf('Base path: %s', $kernel->basePath()));
        $output->line(sprintf('Environment: %s', $kernel->environment()));

        return 0;
    }
}
