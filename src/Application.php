<?php

declare(strict_types=1);

namespace Velt\Cli;

use Velt\Cli\Command\Command;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;
use Throwable;

/**
 * Cœur de la CLI - Orchestre les commandes
 * 
 * Cette classe est responsable de:
 *   1. Garder la liste de toutes les commandes enregistrées
 *   2. Parser les arguments du terminal via Input
 *   3. Trouver la bonne commande
 *   4. L'exécuter et retourner le code d'erreur
 * 

 */
final class Application
{
    /** @var array<string, Command> Liste de toutes les commandes disponibles */
    private array $commands = [];

    public function __construct(
        private readonly string $name,
        private readonly string $version,
        private readonly ?Output $output = null,
    ) {
    }

    /**
     * Enregistre une nouvelle commande
     * 
     * Exemple: register(new MakeControllerCommand()) permet de faire
     *          php bin/velt make:controller User
     */
    public function register(Command $command): void
    {
        $this->commands[$command->name()] = $command;
    }

    /**
     * Lance la CLI avec les arguments du terminal
     * 
     * Exemple: run(['bin/velt', 'make:controller', 'User'])
     * 
     * Retourne:
     *   0 = succès
     *   1 = erreur
     */
    public function run(array $argv, ?Output $output = null): int
    {
        // Prépare l'affichage des messages
        $io = $output ?? $this->output ?? new Output();
        
        // Parse les arguments du terminal en données utilisables
        // Exemple: ['bin/velt', 'make:controller', 'User'] devient
        //          command='make:controller', argument[0]='User'
        $input = Input::fromArgv($argv);
        
        // Récupère le nom de la commande, 'list' par défaut si vide
        $commandName = $input->command() ?? 'list';

        // Cherche la commande dans la liste enregistrée
        $command = $this->commands[$commandName] ?? null;

        // Si la commande n'existe pas, affiche une erreur
        if ($command === null) {
            $io->error(sprintf('Unknown command "%s".', $commandName));
            $io->line('Run "php bin/velt list" to see available commands.');

            return 1;
        }

        // Lance la commande trouvée
        // Elle retournera 0 (succès) ou 1 (erreur)
        try {
            return $command->run($input, $io);
        } catch (Throwable $exception) {
            // Si une exception est levée, on l'attrape et on affiche l'erreur
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
