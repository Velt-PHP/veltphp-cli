# Issue 03 - Creer serve et make:test

## Labels

`module:1-foundations`, `area:cli`, `type:feature`, `type:tests`, `priority:p1`, `status:ready`

## Objectif

Ajouter les commandes MVP pour lancer le serveur local et generer un test.

## Commandes cible

```bash
php bin/velt serve
php bin/velt serve --host=127.0.0.1 --port=8000
php bin/velt make:test UserControllerTest
```

## Travail attendu

- `serve` lance le serveur PHP local sur le dossier public.
- `serve` affiche l'URL disponible.
- `make:test` cree un fichier PHPUnit dans `tests/Feature`.
- Le test genere contient une methode exemple.

## Contraintes

- Ne pas lancer un serveur infini dans les tests automatises.
- Ne pas imposer Docker pour le MVP.
- Le serveur local doit etre une aide developpeur, pas une dependance de production.

## Criteres d'acceptation

- La commande `serve` construit la commande PHP correcte.
- La commande `make:test` n'ecrase pas un test existant sans option explicite.
- Le fichier genere respecte PHPUnit.
- Les erreurs sont lisibles si le dossier `public/` manque.

## Definition of Done

- Commandes implementees.
- Tests de generation.
- Documentation des options.

