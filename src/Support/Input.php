<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

/**
 * Parseur d'arguments du terminal
 * 
 * Transforme les arguments bruts en données faciles à utiliser.
 * 
 * Exemple d'entrée:
 *   ['bin/velt', 'make:controller', 'User', '--path=/mon-projet', '--force']
 * 
 * Devient:
 *   command = 'make:controller'
 *   arguments = ['User']
 *   options = ['path' => '/mon-projet', 'force' => true]
 */
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

    /**
     * Parse les arguments du terminal ($_SERVER['argv'])
     * 
     * Exemple:
     *   fromArgv(['bin/velt', 'make:controller', 'User', '--force'])
     */
    public static function fromArgv(array $argv): self
    {
        // Retire le premier élément (bin/velt) et récupère le reste
        $tokens = array_values(array_slice($argv, 1));
        $command = null;
        $arguments = [];
        $options = [];

        // Traite chaque paramètre
        foreach ($tokens as $token) {
            if (!is_string($token)) {
                continue;
            }

            // Vérifie si c'est une option (commence par --)
            if (str_starts_with($token, '--')) {
                // Enlève les -- et sépare nom et valeur
                // Exemple: --path=/mon-projet devient ['path', '/mon-projet']
                $option = substr($token, 2);
                [$name, $value] = array_pad(explode('=', $option, 2), 2, true);
                $options[$name] = $value;

                continue;
            }

            // Le premier token non-option est la commande
            if ($command === null) {
                $command = $token;
                continue;
            }

            // Le reste sont des arguments
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
