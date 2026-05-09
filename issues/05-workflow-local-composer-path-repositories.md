# Issue 05 - Documenter workflow local Composer path repositories

## Labels

`module:1-foundations`, `area:cli`, `area:composer`, `type:documentation`, `priority:p0`, `status:ready`

## Objectif

Documenter comment la CLI et les autres packages Velt sont installes ensemble en local pendant le developpement.

## Travail attendu

- Ajouter un guide CLI pour tester `vendor/bin/velt`.
- Montrer un `composer.json` sandbox utilisant `path repositories`.
- Expliquer `composer dump-autoload`.
- Expliquer comment tester les generateurs dans un dossier temporaire.
- Relier ce guide au sous-module `07-integration-quality`.

## Criteres d'acceptation

- Un membre de l'equipe peut installer la CLI locale sans Packagist.
- `vendor/bin/velt list` fonctionne dans la sandbox.
- Les generateurs peuvent etre testes sans skeleton final.

## Definition of Done

- Guide ajoute dans README CLI.
- Exemple Composer valide.
- Reference vers la convention inter-modules ajoutee.

