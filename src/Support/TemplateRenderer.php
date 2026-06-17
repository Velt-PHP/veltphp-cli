<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

use RuntimeException;

/**
 * Moteur de templates
 * 
 * Charge les fichiers .stub (templates) et remplace les variables.
 * 
 * Exemple:
 *   Template: "namespace {NAMESPACE}; class {CLASS_NAME} {}"
 *   Variables: ['NAMESPACE' => 'App', 'CLASS_NAME' => 'User']
 *   Résultat: "namespace App; class User {}"
 */
final class TemplateRenderer
{
    public function __construct(private readonly string $templatesPath)
    {
    }

    /**
     * Rend un template en remplaçant les variables
     * 
     * Exemple:
     *   render('controller.php.stub', ['CLASS_NAME' => 'UserController'])
     *   
     * @param array<string, string> $variables Les variables à remplacer
     */
    public function render(string $template, array $variables): string
    {
        // Construit le chemin complet du template
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $template);

        // Vérifie que le template existe
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Template not found: %s', $template));
        }

        // Lit le contenu du template
        $contents = (string) file_get_contents($path);

        // Remplace chaque variable par sa valeur
        // {CLASS_NAME} devient UserController, {NAMESPACE} devient App\Controllers, etc.
        foreach ($variables as $key => $value) {
            $contents = str_replace('{' . $key . '}', $value, $contents);
        }

        return $contents;
    }
}
