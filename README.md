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

## Choix technique Module 1

La CLI utilise un registry de commandes maison et minimal, pas Symfony Console. Ce choix garde le package sans dependance runtime pour le Module 1, rend les commandes faciles a tester sans terminal interactif, et suffit pour les besoins MVP : `list`, `help`, generateurs et `serve`.

Le package reste compatible Composer grace a la declaration `bin` :

```json
"bin": [
    "bin/velt"
]
```

## Installation locale

Depuis le repo CLI :

```bash
composer install
php bin/velt list
vendor/bin/velt list
```

Le binaire `bin/velt` contient un autoloader de secours. Cela permet d'executer `php bin/velt list` meme avant l'installation Composer, utile pour verifier rapidement le repo CLI seul.

## Commandes MVP

```bash
php bin/velt list
php bin/velt help make:feature
php bin/velt make:feature auth --no-interaction
php bin/velt make:feature auth --force
php bin/velt make:controller UserController
php bin/velt make:model User
php bin/velt make:test UserControllerTest
php bin/velt serve --host=127.0.0.1 --port=8000
```

Toutes les commandes de generation acceptent `--path=/chemin/projet` pour ecrire dans une sandbox ou un skeleton sans supposer que la CLI est lancee depuis la racine du projet cible.

La commande `serve` lance le serveur PHP local sur `public/`. Pour les tests automatises, `--dry-run` affiche la commande construite sans demarrer de processus long :

```bash
php bin/velt serve --path=/tmp/velt-sandbox --dry-run
```

## Fichiers generes par `make:feature`

```text
features/auth/
  AuthController.php
  AuthService.php
  AuthModel.php
  views/
    login.velt.php
  tests/
    AuthControllerTest.php
```

Le fichier `login.velt.php` respecte la syntaxe officielle declarative du Module 1 avec `Page::make()`, `Card::make()`, `Text::make()` et `Button::make()`.

Aucun fichier existant n'est ecrase sans `--force`.

## Workflow local Composer path repositories

Tant que les packages Velt ne sont pas publies sur Packagist, une sandbox ou le futur skeleton peut utiliser des `path repositories`.

Exemple de `composer.json` sandbox :

```json
{
    "name": "veltphp/sandbox",
    "type": "project",
    "require": {
        "php": "^8.2",
        "veltphp/cli": "*"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../github-repos/veltphp-cli",
            "options": {
                "symlink": true
            }
        }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Puis :

```bash
composer update veltphp/cli
composer dump-autoload
vendor/bin/velt list
vendor/bin/velt make:feature auth --no-interaction
```

`composer dump-autoload` est utile apres l'ajout ou le deplacement de classes dans un package local. Le symlink garde la sandbox branchee directement sur le repo en cours de developpement.

## Tester les generateurs sans skeleton final

Le Module 2 fournira le skeleton complet. En Module 1, on teste la CLI dans un dossier temporaire :

```bash
mkdir /tmp/velt-cli-sandbox
php bin/velt make:feature auth --path=/tmp/velt-cli-sandbox --no-interaction
php bin/velt make:test UserControllerTest --path=/tmp/velt-cli-sandbox
```

Pour `serve`, creer seulement un dossier `public/` dans la sandbox suffit pour valider la commande :

```bash
mkdir /tmp/velt-cli-sandbox/public
php bin/velt serve --path=/tmp/velt-cli-sandbox --dry-run
```

Ce workflow est relie au sous-module `07-integration-quality`, dont le role est de verifier que les repos Module 1 restent installables ensemble via Composer `path repositories`, sans dependances circulaires et avec des tests automatisables.

## Tests

```bash
composer test
```

Les tests couvrent :

- affichage `list` et `help` ;
- code d'erreur pour commande inconnue ;
- generation `make:feature` avec filesystem temporaire ;
- protection anti-ecrasement sans `--force` ;
- generation `make:test` ;
- validation `serve --dry-run` et erreur si `public/` manque.
