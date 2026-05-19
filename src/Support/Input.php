<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

final class Input
{
    /**
     * @param list<string> $arguments
     * @param array<string, string|bool> $options
     */
    private function __construct(
        private readonly ?string $command,
        private readonly array $arguments,
        private readonly array $options,
    ) {
    }

    public static function fromArgv(array $argv): self
    {
        $tokens = array_values(array_slice($argv, 1));
        $command = null;
        $arguments = [];
        $options = [];

        foreach ($tokens as $token) {
            if (!is_string($token)) {
                continue;
            }

            if (str_starts_with($token, '--')) {
                $option = substr($token, 2);
                [$name, $value] = array_pad(explode('=', $option, 2), 2, true);
                $options[$name] = $value;

                continue;
            }

            if ($command === null) {
                $command = $token;

                continue;
            }

            $arguments[] = $token;
        }

        return new self($command, $arguments, $options);
    }

    public function command(): ?string
    {
        return $this->command;
    }

    public function argument(int $index): ?string
    {
        return $this->arguments[$index] ?? null;
    }

    public function option(string $name, string|bool|null $default = null): string|bool|null
    {
        return $this->options[$name] ?? $default;
    }

    public function boolOption(string $name): bool
    {
        return array_key_exists($name, $this->options) && $this->options[$name] !== 'false';
    }

    public function pathOption(): string
    {
        $path = $this->option('path', getcwd() ?: '.');

        return rtrim((string) $path, DIRECTORY_SEPARATOR . '/\\');
    }
}
