<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Input;
use Velt\Cli\Support\Output;

/**
 * Interface pour toutes les commandes de la CLI
 * 
 * Chaque commande doit implémenter ces 4 méthodes pour fonctionner.
 * 
 * Exemple d'une commande:
 *   class MakeControllerCommand implements Command { ... }
 * 
 * Quand l'utilisateur tape: php bin/velt make:controller User
 *   - name() retourne 'make:controller'
 *   - run() est appelé avec les arguments et affiche le résultat
 */
interface Command
{
    /**
     * Retourne le nom de la commande (ex: 'make:controller', 'make:model')
     */
    public function name(): string;

    /**
     * Retourne une courte description (affichée dans la liste des commandes)
     */
    public function description(): string;

    /**
     * Retourne l'aide détaillée (comment utiliser la commande)
     */
    public function help(): string;

    /**
     * Lance la commande
     * 
     * Retourne:
     *   0 = succès
     *   1 = erreur
     */
    public function run(Input $input, Output $output): int;
}
