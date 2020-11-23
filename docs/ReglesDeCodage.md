// Mettre ici les directives de codage choisies (psr, etc ...)

## Générale
- PSR-12
- PSR-4
- l'application sera uniquement en français
- typage des variables et méthodes (paramètres et retour)
- codage de type défensif
- les interfaces se termine par "Interface"
- les classes abstraites commence par "Abstract"
- les noms de variables, constantes, méthodes, classes et autres sont en anglais
- les commentaires et les textes d'erreur sont en français
- les noms de variables, constantes, méthodes doivent être facilement compréhensible mais aussi le plus court possible
- il faut respecter la convention de mettre un verbe au début des méthodes (is,set,get,has,create,build,...)

## Commentaire
- les commentaires sont obligatoires pour les variables, constantes, méthodes, classes
- utilisation de la symtaxe de commentaire de PHPDoc
- les commentaires indiquerons ce que représente la classe, variable, constante ou méthode plus le type de ceux-ci
- pour les méthodes, les paramètres typés, le retour typé et les exceptions typées seront à indiqués
- utilisation de `void` si rien n'est retourné pour les méthodes

## Format des commentaires
- Méthode
    * `/**`
    * `* Description` sauf si explicite par exemple dans le cas des setter et getter
    * `* @param string $test` Ce que représente ce paramètre sauf si explicite
    * `* @param int|null $id` Le `|` signifie "ou" dans le cas où un paramètre est typé "`?int`", c'est à dire nullable
    * `* @throws Exception` Condition de déclenchement
    * `* @return Object[]` Précision sur le retour sauf si explicite, par exemple dans le cas d'un tableau, on peut mettre "`Tableau à 1 dimension indexé numériquement`"
    * `*/`
- Classe
    * `/**`
    * `* @author Nom Prénom`
    * `* Description`
    * `*/`
- Variable
    * `/**`
    * `* @var Object` Ce que représente cette variable sauf si explicite
    * `*/`
- Constante
    * `/**`
    * `* @const string` Ce que représente cette variable sauf si explicite
    * `*/`
