# Sous-module 03 - CLI Generators

## Mission

Ce sous-module fournit la CLI officielle de Velt. Elle doit donner une experience proche d'Artisan, sans copier Laravel aveuglement.

La CLI doit initialiser un projet, generer les fichiers MVP et preparer les commandes de preview.

La CLI est aussi responsable de rendre le workflow local credible. Elle doit aider l'equipe a tester les packages via Composer, generer du code conforme aux contrats publics et ne jamais cacher une dependance directe non documentee.

## Perimetre

Inclus :

- executable `bin/velt` ;
- commande `new` ;
- commande `make:feature` ;
- commande `make:controller` ;
- commande `make:model` ;
- commande `make:test` ;
- commande `serve` ;
- templates de generation ;
- documentation `path repositories` cote skeleton/sandbox ;
- commandes automatisables avec `--no-interaction`.

Exclus :

- generateur ORM complet ;
- scaffold d'auth complet ;
- microservices ;
- hot reload.

## Comment tester sans skeleton complet

La CLI peut etre testee avant le Module 2 avec un dossier temporaire.

- `php bin/velt list` doit fonctionner dans le repo CLI seul.
- `vendor/bin/velt list` doit fonctionner apres installation Composer locale.
- Les commandes de generation doivent ecrire dans un dossier temporaire et verifier le contenu genere.
- `make:feature auth --no-interaction` doit fonctionner sans demander de saisie.
- `serve` peut etre teste en validant la commande construite, sans lancer un serveur long dans les tests unitaires.
- `preview` peut utiliser un faux service preview qui retourne une URL et un payload QR-ready.

La CLI ne doit pas charger directement le moteur UI ou database pour tester ses generateurs. Elle genere des fichiers respectant leurs contrats ; l'execution reelle est verifiee dans les tests d'integration.

## Issues

- [Issue 01 - Initialiser la CLI php velt](issues/01-initialiser-cli-php-velt.md)
- [Issue 02 - Creer make feature](issues/02-creer-make-feature.md)
- [Issue 03 - Creer serve et make test](issues/03-creer-serve-make-test.md)
- [Issue 04 - Preparer distribution Composer de la CLI](issues/04-preparer-distribution-composer-cli.md)
- [Issue 05 - Documenter workflow local Composer path repositories](issues/05-workflow-local-composer-path-repositories.md)
