# Issue 01 - Initialiser la CLI php velt

## Labels

`module:1-foundations`, `area:cli`, `type:feature`, `priority:p0`, `status:ready`

## Objectif

Creer l'entree CLI officielle du framework : `php velt` ou `php bin/velt`.

## Travail attendu

- Creer le script executable `bin/velt`.
- Creer une classe `Console\Application`.
- Ajouter un systeme simple de commandes.
- Ajouter les commandes `list` et `help`.
- Afficher version, nom du framework et commandes disponibles.

## Contraintes

- Symfony Console peut etre accepte si le projet decide de reduire le cout technique, mais le choix doit etre documente.
- Si implementation maison, rester minimal.
- Les commandes doivent etre testables sans lancer un vrai terminal interactif.

## Criteres d'acceptation

- `php bin/velt list` affiche les commandes.
- `php bin/velt help make:feature` affiche l'aide de la commande.
- Une commande inconnue retourne un code de sortie non zero.
- Le style de sortie reste sobre et professionnel.

## Definition of Done

- CLI executable.
- Command registry fonctionnel.
- Tests de sortie basiques.
- README mis a jour.

