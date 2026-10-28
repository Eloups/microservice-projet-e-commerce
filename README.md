# TP Architectures logicielles

Ce code représente le microservice **Cart** de notre projet e-commerce.

Ce microservice comprend la gestion des paniers de notre site. 

## Authors

- TISSIER Elouan
- LESPLINGUIES Tristan

## Installation
### Prérequis

- PHP 8.5 ou supérieur (extension `pdo_sqlite` activée)
- Composer

### Étapes

```bash
# 1. Installer les dependances
composer install

# 2. Initialiser les bases SQLite
#    - data/ecommerce.sqlite : donnees metier (users, articles, carts, cart_articles)
#    - data/events.sqlite    : evenements de l'event bus
composer run db-init

# 3. Lancer le serveur de developpement sur http://127.0.0.1:3000
php -S 127.0.0.1:3000 index.php
```

### Autres commandes

```bash
# Analyse statique (phpstan)
composer run lint

# Reinitialiser les deux bases (efface paniers et evenements, reinsert le seed)
composer run db-init
```

### Tester rapidement l'API

Les requetes sont preconfigurees pour **Yaak** dans le dossier [_yaak](_yaak/) :
importez ce dossier dans Yaak (File > Import), le workspace **TP-architecture**
contient les deux appels ci-dessous, prets a executer contre `127.0.0.1:3000`.

Ou en ligne de commande avec curl :

```bash
# Ajouter un article a un panier
curl -X POST http://127.0.0.1:3000/cart/addArticle \
  -H "Content-Type: application/json" \
  -d '{"cartId": 1, "articleId": 2}'

# Valider un panier (consomme ses evenements en attente)
curl -X POST http://127.0.0.1:3000/cart/validate \
  -H "Content-Type: application/json" \
  -d '{"cartId": 1}'
```

## Localisation travail demandé
### Ports
#### IN

- Router => [src/domain/ports/IPortINCartCases.php](src/domain/ports/IPortINCartCases.php)

#### OUT

- Database => [src/domain/ports/IPortOUTDatabase.php](src/domain/ports/IPortOUTDatabase.php)
- Event Bus => [src/domain/ports/IPortOUTEvent.php](src/domain/ports/IPortOUTEvent.php)

### Design pattern

- Use Case pattern => [src/domain/useCases/](src/domain/useCases/)
- Adapter pattern => [src/adapters/](src/adapters/)

### Evénements

- Publier
  - Lors d'un ajout d'article => [src/domain/useCases/AddArticleToCartUseCase.php ligne 34](src/domain/useCases/AddArticleToCartUseCase.php)
  - Lors d'une validation de panier => [src/domain/useCases/ValidateCartUseCase.php ligne 39](src/domain/useCases/ValidateCartUseCase.php)
- Consommer
  - La route validation d'un panier est créé pour être appelée par des évènements de l'event BUS
    => [src/domain/useCases/ValidateCartUseCase.php ligne 28](src/domain/useCases/ValidateCartUseCase.php)
    (declenchee par la route [POST /cart/validate, index.php ligne 37](index.php))

### Simulation de panne

Le script [data/panne-on.php](data/panne-on.php) (commande `composer run panne-on`)
simule une panne du stockage metier : il pose un verrou **EXCLUSIF** sur
`data/ecommerce.sqlite` dans une transaction ouverte. Tant qu'il tourne :

- toute requete sur la base metier echoue (`database is locked`) apres 2 s
  (delai force via `PDO::ATTR_TIMEOUT` dans
  [src/database/sqlite/SqliteDatabasePDO.php](src/database/sqlite/SqliteDatabasePDO.php))
- le service repond `503` proprement via le catch `PDOException` d'[index.php](index.php)
- `data/events.sqlite` (event bus, fichier separé) reste operationnel

`Ctrl+C` (ou tuer le script) leve la panne : la transaction est annulee,
le verrou est libere et le service repart sans perte de donnees.

### tracing d'un flux

TODO

## Démonstrations
### Action port IN

TODO

### Publication d'un événement

TODO

### Interaction externe

Un consommateur externe (autre microservice, batch...) consulte la file
d'evenements via le port entrant
[IPortINEventCases](src/domain/ports/IPortINEventCases.php) expose par la
route `GET /events/pending?limit=10` (repond 200 meme pendant une panne
de la base metier) :

```bash
curl "http://127.0.0.1:3000/events/pending"
```

### Panne simulée + comportement résilient

Scenario complet (serveur lance avec `php -S 127.0.0.1:3000 index.php`) :

1. `POST /cart/addArticle` -> `200` (nominal)
2. `composer run panne-on` dans un autre terminal -> PANNE ACTIVE
3. `POST /cart/addArticle` -> `503 {"code":503,"message":"Service momentanement indisponible..."}`
   - degradation propre : aucun crash, l'evenement n'est pas publie
     (l'ajout echoue avant la publication)
4. `GET /events/pending` -> `200` : l'event bus (base separee) reste
   joignable pendant la panne (isolation)
5. `Ctrl+C` sur le script de panne
6. `POST /cart/addArticle` -> `200` : reprise sans perte

### Logs corrélés

Chaque requete recoit un identifiant unique (`X-Request-Id`, fourni par le
client ou genere dans [index.php](index.php)) :

- renvoye dans l'en-tete de reponse
- journalise dans `data/logs/app.log` par
  [src/utils/Logger.php](src/utils/Logger.php) (lignes JSON)
- toutes les erreurs passer par `ErrorResponse` sont logguees

Exemple : la requete `req_1f3caa...` ci-dessous est tracable de bout en
bout, y compris sa degradation 503 pendant la panne :

```json
{"timestamp":"...","level":"info","requestId":"req_1f3caa378d613cb2","message":"requete recue","method":"POST","path":"/cart/addArticle"}
{"timestamp":"...","level":"error","requestId":"req_1f3caa378d613cb2","message":"Service momentanement indisponible (stockage indisponible)","code":503,"status":503}
```
