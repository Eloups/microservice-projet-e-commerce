# TP Architectures logicielles

Ce code représente le microservice **Cart** de notre projet e-commerce.

Ce microservice comprend la gestion des paniers de notre site. 

## Authors

- TISSIER Elouan
- LESPLINGUIES Tristan

## Installation
### Prérequis

- PHP 8.1 ou supérieur (extension `pdo_sqlite` activée)
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

TODO

### tracing d'un flux

TODO

## Démonstrations
### Action port IN

TODO

### Publication d'un événement

TODO

### Interaction externe

TODO

### Panne simulée + comportement résilient

TODO

### Logs corrélés

TODO
