<?php

declare(strict_types=1);

namespace Velt\Cli\Command;

use Velt\Cli\Support\Filesystem;
use Velt\Cli\Support\Input;
use Velt\Cli\Support\Naming;
use Velt\Cli\Support\Output;
use Velt\Cli\Support\TemplateRenderer;

/**
 * Commande: make:controller
 * 
 * Génère un contrôleur PHP dans le dossier app/Controllers/
 * 
 * Utilisation:
 *   php bin/velt make:controller User
 *   php bin/velt make:controller UserProfile --force
 *   php bin/velt make:controller Auth --path=/mon-projet
 * 
 * Crée automatiquement le namespace, la classe, etc.
 */
final class MakeControllerCommand implements Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    /**
     * Le nom de la commande tel que l'utilisateur le tape
     */
    public function name(): string
    {
        return 'make:controller';
    }

    /**
     * Description courte affichée dans la liste des commandes
     */
    public function description(): string
    {
        return 'Generate a Velt controller.';
    }

    /**
     * Aide détaillée avec des exemples
     */
    public function help(): string
    {
        return 'Usage: php bin/velt make:controller <name> [--force] [--path=/project]';
    }

    /**
     * Lance la commande make:controller
     * 
     *   1. Récupère le nom saisi par l'utilisateur
     *   2. Transforme le nom (user → User, user-profile → UserProfile)
     *   3. Ajoute le suffixe 'Controller' (User → UserController)
     *   4. Construit le chemin: app/Controllers/UserController.php
     *   5. Vérifie si le fichier existe déjà
     *   6. Charge le template controller.php.stub
     *   7. Remplace {CLASS_NAME} et {NAMESPACE} dans le template
     *   8. Écrit le fichier sur le disque
     *   9. Affiche un message de succès
     */
    public function run(Input $input, Output $output): int
    {
        // Étape 1: Récupère le nom du contrôleur depuis les arguments
        // Exemple: php bin/velt make:controller User
        $name = $input->argument(0);

        // Valide que le nom est fourni
        if ($name === null || trim($name) === '') {
            $output->error('The controller name is required.');
            return 1;
        }

        // Étape 2-3: Transforme le nom
        // 'user' → 'User' → 'UserController'
        // 'user-profile' → 'UserProfile' → 'UserProfileController'
        $className = Naming::suffix(Naming::pascal($name), 'Controller');
        
        // Étape 4: Construit le chemin complet où le fichier sera créé
        $target = $input->pathOption() 
            . DIRECTORY_SEPARATOR . 'app' 
            . DIRECTORY_SEPARATOR . 'Controllers' 
            . DIRECTORY_SEPARATOR . $className . '.php';

        // Étape 5: Vérifie si le fichier existe (sans --force)
        if ($this->filesystem->exists($target) && !$input->boolOption('force')) {
            $output->error(sprintf('Controller "%s" already exists. Use --force to overwrite it.', $className));
            return 1;
        }

        // Étapes 6-8: Charge le template, le remplit, et écrit le fichier
        $this->filesystem->write($target, $this->renderer->render('controller.php.stub', [
            'CLASS_NAME' => $className,                  // UserController
            'NAMESPACE' => 'App\\Controllers',           // App\Controllers
        ]));

        // Étape 9: Affiche un message de succès
        $output->success(sprintf('Controller "%s" generated.', $className));

        // Retourne 0 (succès)
        return 0;
    }
}
