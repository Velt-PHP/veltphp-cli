# Sous-module 03 - CLI Generators

## Mission

Ce sous-module fournit la CLI officielle de Velt. Elle doit donner une experience proche d'Artisan, sans copier Laravel aveuglement.

La CLI doit initialiser un projet, generer les fichiers MVP et preparer les commandes de preview.

## Perimetre

Inclus :

- executable `bin/velt` ;
- commande `new` ;
- commande `make:feature` ;
- commande `make:controller` ;
- commande `make:model` ;
- commande `make:test` ;
- commande `serve` ;
- templates de generation.

Exclus :

- generateur ORM complet ;
- scaffold d'auth complet ;
- microservices ;
- hot reload.

## Issues

- [Issue 01 - Initialiser la CLI php velt](issues/01-initialiser-cli-php-velt.md)
- [Issue 02 - Creer make feature](issues/02-creer-make-feature.md)
- [Issue 03 - Creer serve et make test](issues/03-creer-serve-make-test.md)

