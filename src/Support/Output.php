<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

/**
 * Gestionnaire d'affichage des messages
 * 
 * Affiche les messages formatés dans le terminal.
 * Garde aussi un "buffer" (stockage) pour les tests.
 */
final class Output
{
    // Stocke les messages affichés (utile pour les tests)
    private string $buffer = '';

    /**
     * Affiche une ligne de texte simple
     */
    public function line(string $message = ''): void
    {
        // Stocke dans le buffer (pour les tests)
        $this->buffer .= $message . PHP_EOL;
        // Affiche dans le terminal
        fwrite(STDOUT, $message . PHP_EOL);
    }

    /**
     * Affiche un message d'erreur formaté
     * Affichage: [ERROR] Votre message
     */
    public function error(string $message): void
    {
        $this->line('[ERROR] ' . $message);
    }

    /**
     * Affiche un message de succès formaté
     * Affichage: [OK] Votre message
     */
    public function success(string $message): void
    {
        $this->line('[OK] ' . $message);
    }

    public function buffer(): string
    {
        return $this->buffer;
    }
}
