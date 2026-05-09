# Issue 04 - Preparer distribution Composer de la CLI

## Labels

`module:1-foundations`, `area:cli`, `type:feature`, `priority:p0`, `status:ready`

## Objectif

Rendre la CLI executable de maniere propre depuis un package Composer.

## Pourquoi cette issue est obligatoire

Une CLI qui fonctionne seulement dans un dossier local n'est pas suffisante pour un framework. Elle doit pouvoir etre appelee depuis `vendor/bin/velt`, puis etre exposee dans le skeleton.

## Travail attendu

- Declarer le binaire dans `composer.json`.
- S'assurer que `bin/velt` fonctionne sous Windows, Linux et macOS.
- Ajouter une option `--no-interaction` pour les commandes automatisables.
- Ajouter codes de sortie coherents : `0` succes, `1` erreur.
- Documenter comment tester la CLI localement avec Composer path repository.

## Contraintes

- Ne pas implementer `velt new` complet ici si le skeleton Module 2 n'est pas pret.
- Ne pas coder de chemins absolus.
- Ne pas supposer que le projet est toujours lance depuis la racine.

## Criteres d'acceptation

- `vendor/bin/velt list` fonctionne.
- `php bin/velt list` fonctionne.
- Une commande inconnue retourne un code de sortie non zero.
- Les commandes peuvent etre testees sans interaction utilisateur.
- La documentation explique comment brancher la CLI au skeleton.

## Definition of Done

- Binaire Composer configure.
- Tests CLI verts.
- Documentation de distribution ajoutee.

