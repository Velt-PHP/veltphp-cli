<?php

declare(strict_types=1);

namespace Velt\Cli\Command\Concerns;

use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

trait WritesGeneratedFiles
{
    private function writeGeneratedFile(
        Filesystem $filesystem,
        Input $input,
        Output $output,
        string $target,
        string $contents,
        string $label,
    ): bool {
        if ($filesystem->exists($target) && !$input->boolOption('force')) {
            $output->error(sprintf('%s already exists. Use --force to overwrite it.', $label));

            return false;
        }

        $filesystem->write($target, $contents);

        return true;
    }
}
