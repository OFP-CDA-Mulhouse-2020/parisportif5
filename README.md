# parisportif5


Cahier des charges : https://github.com/OFP-CDA-Mulhouse-2020/specs


## Régles de codage
Lien vers [les régles de codage](ReglesDeCodage.md)

## Programme utilisé
- Git & Git Flow : pour le versionning
- PHPUnit : pour les tests

## Git

### Branches principales
- main
- develop

### Nommage des branches
- feature/nom-feature
- bugfix/nom-bugfix
- hotfix/nom-hotfix

### Format recommandé des messages de commit
Date du commit (format=>'yyyy-mm-jj') - #N (N<=>numéro du commit du jour par une personne) - Texte descriptif du commit

exemple : `git commit -m "2020-11-10 - #1 - Mise à jour des fichier md"`

### Commande git flow
- https://github.com/nvie/gitflow

### Commande git
- `git pull origin develop` pour mettre à jour sa branche locale develop
- `git rebase develop` pour mettre à jour les commits de sa branche locale de travail avec ceux du dépôt distant
- `git push origin feature/nom-feature` pour pusher la branche sur le dépôt distant
- Sur github créer une pull request avec un descriptif des changements
- Après validation de la pull request et la fusion du code, si la branche n'est plus utilisée, il faut là supprimée du dépôt distant avec `git push origin --delete feature/nom-feature`
- Puis `git fetch --prune` pour nettoyé dans le dossier locale les refs des branches distantes supprimées

### Pull Request sur Github
- créer une pull request avec un descriptif des changements (fichiers impactés et modifications apportées)
- titre de la pull request avec le format : type-de-changement(feature ou bugfix ou hotfix) : Nom des changments(ex: Modification des headers)


information version php 7.4.9
doctrine annotations
phpunit bridge
phpunit watcher fichier config OK
phpstan 
phpstan-symfony
phpstan extension
codesniffer