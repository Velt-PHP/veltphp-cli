# Issue 02 - Creer make:feature

## Labels

`module:1-foundations`, `area:cli`, `type:feature`, `priority:p0`, `status:ready`

## Objectif

Generer une feature Velt autonome selon l'approche feature based du MVP.

## Commande cible

```bash
php bin/velt make:feature auth
```

## Fichiers a generer

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

## Contraintes de syntaxe UI

Le fichier `login.velt.php` doit utiliser la syntaxe officielle declarative :

```php
return Page::make('Connexion')
    ->layout('auth')
    ->add(Card::make()->children([
        Text::make('Connexion')->as('h1'),
        Button::make('Se connecter')->type('submit')->variant('primary'),
    ]));
```

## Travail attendu

- Creer des templates de fichiers.
- Remplacer les variables `{FEATURE_NAME}`, `{CLASS_NAME}`, `{NAMESPACE}`.
- Verifier que la feature n'existe pas deja.
- Proposer une option `--force` pour ecraser volontairement.

## Criteres d'acceptation

- La commande genere une feature complete.
- Les noms de classes respectent le PascalCase.
- Les dossiers respectent le kebab/lowercase selon convention.
- Aucun fichier existant n'est ecrase sans `--force`.

## Definition of Done

- Commande implementee.
- Templates documentes.
- Tests avec filesystem temporaire.

