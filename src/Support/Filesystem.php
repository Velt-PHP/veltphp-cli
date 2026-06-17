<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

use RuntimeException;

/**
 * Gestionnaire de fichiers
 * 
 * Responsable de:
 *   1. Vérifier si un fichier existe
 *   2. Créer les dossiers nécessaires
 *   3. Écrire les fichiers sur le disque
 */
final class Filesystem
{
    /**
     * Vérifie si un fichier ou dossier existe
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Écrit un fichier sur le disque
     * 
     * Crée automatiquement les dossiers s'ils n'existent pas.
     * 
     * Exemple: write('app/Controllers/User.php', '<php code>')
     *          Crée le dossier app/Controllers/ s'il n'existe pas
     *          Écrit le fichier User.php
     */
    public function write(string $path, string $contents): void
    {
        // Récupère le dossier parent du fichier
        $directory = dirname($path);

        // Crée le dossier s'il n'existe pas
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create directory: %s', $directory));
        }

        // Écrit le contenu dans le fichier
        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException(sprintf('Unable to write file: %s', $path));
        }
    }
}
