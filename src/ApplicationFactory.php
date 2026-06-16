<?php

declare(strict_types=1);

namespace Velt\Cli;

use Velt\Cli\Command\HelpCommand;
use Velt\Cli\Command\KernelCheckCommand;
use Velt\Cli\Command\DbSeedCommand;
use Velt\Cli\Command\ListCommand;
use Velt\Cli\Command\MakeControllerCommand;
use Velt\Cli\Command\MakeFeatureCommand;
use Velt\Cli\Command\MakeMigrationCommand;
use Velt\Cli\Command\MakeModelCommand;
use Velt\Cli\Command\MakePageCommand;
use Velt\Cli\Command\MakeSeederCommand;
use Velt\Cli\Command\MakeServiceCommand;
use Velt\Cli\Command\MakeTestCommand;
use Velt\Cli\Command\MigrateCommand;
use Velt\Cli\Command\MigrateRollbackCommand;
use Velt\Cli\Command\ServeCommand;
use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\ProjectRuntime;
use Velt\Cli\Support\TemplateRenderer;

/**
 * Fabrique d'Application - Crée et configure toute la CLI
 * 
 * Cette classe est responsable de:
 *   1. Créer l'Application avec le nom et la version
 *   2. Créer les services réutilisables (Filesystem, TemplateRenderer)
 *   3. Enregistrer toutes les commandes disponibles
 * 
 * C'est le point central de configuration de la CLI.
 * Un peu comme un "chef de projet" qui prépare tous les outils avant le démarrage.
 */
final class ApplicationFactory
{
    /**
     * Crée une nouvelle instance de la CLI avec toutes les commandes enregistrées
     */
    public static function create(): Application
    {
        // Prépare le chemin des templates (fichiers .stub)
        $templatesPath = dirname(__DIR__) . '/resources/templates';
        
        // Crée le helper pour écrire des fichiers sur le disque
        $filesystem = new Filesystem();
        
        // Crée le helper pour remplir les templates avec des variables
        $renderer = new TemplateRenderer($templatesPath);
        $runtime = new ProjectRuntime();

        // Crée l'application CLI principal
        $application = new Application('Velt CLI', '0.1.0');
        
        // Enregistre chaque commande disponible
        // L'Application utilisera ces commandes pour répondre à l'utilisateur
        $application->register(new ListCommand($application));           // php bin/velt list
        $application->register(new HelpCommand($application));          // php bin/velt help make:controller
        $application->register(new KernelCheckCommand());               // php bin/velt kernel:check
        $application->register(new MakeFeatureCommand($filesystem, $renderer));     // php bin/velt make:feature auth
        $application->register(new MakeControllerCommand($filesystem, $renderer));  // php bin/velt make:controller User
        $application->register(new MakeModelCommand($filesystem, $renderer));       // php bin/velt make:model Post
        $application->register(new MakePageCommand($filesystem, $renderer));        // php bin/velt make:page Home
        $application->register(new MakeServiceCommand($filesystem, $renderer));     // php bin/velt make:service Billing
        $application->register(new MakeTestCommand($filesystem, $renderer));        // php bin/velt make:test UserController
        $application->register(new MakeMigrationCommand($filesystem, $renderer));   // php bin/velt make:migration create_users_table
        $application->register(new MigrateCommand($runtime));                       // php bin/velt migrate
        $application->register(new MigrateRollbackCommand($runtime));               // php bin/velt migrate:rollback
        $application->register(new MakeSeederCommand($filesystem, $renderer));      // php bin/velt make:seeder UserSeeder
        $application->register(new DbSeedCommand($runtime));                        // php bin/velt db:seed
        $application->register(new ServeCommand());                     // php bin/velt serve

        return $application;
    }
}
