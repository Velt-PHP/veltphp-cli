<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

use RuntimeException;

final class Filesystem
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function write(string $path, string $contents): void
    {
        $directory = dirname($path);

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create directory: %s', $directory));
        }

        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException(sprintf('Unable to write file: %s', $path));
        }
    }
}
