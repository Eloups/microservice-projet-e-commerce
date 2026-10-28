<?php

declare(strict_types=1);

/**
 * Script d'initialisation de la base SQLite.
 * Recree le fichier de base, les tables et les donnees de demonstration.
 *
 * Usage : composer run init
 */

require __DIR__ . '/../vendor/autoload.php';

use MicroserviceProjetECommerce\Database\Sqlite\SqliteDatabasePDO;
use MicroserviceProjetECommerce\Database\Sqlite\SqliteEventPDO;

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'ecommerce.sqlite';

// Reinitialisation par DROP TABLE (et non par suppression du fichier) :
// sur Windows, unlink() echoue si un serveur (composer dev) garde
// le fichier SQLite ouvert.
$db = new SqliteDatabasePDO($dbPath);
$db->getPDO()->exec('
    DROP TABLE IF EXISTS cart_articles;
    DROP TABLE IF EXISTS carts;
    DROP TABLE IF EXISTS articles;
    DROP TABLE IF EXISTS users;
');
echo 'Tables existantes supprimees' . "\n";

// Recreation explicite du schema puis du seed
$db->createSchema();
$db->seedIfEmpty();

echo "Base creee : $dbPath\n";
echo "Tables : users, articles, carts, cart_articles\n";
echo "Seed insere.\n";

// Base dediee aux evenements de l'event bus (fichier separe)
$eventDbPath = __DIR__ . DIRECTORY_SEPARATOR . 'events.sqlite';
$eventDb = new SqliteEventPDO($eventDbPath);
$eventDb->getPDO()->exec('DROP TABLE IF EXISTS events');
$eventDb->createSchema();

echo "Base evenements creee : $eventDbPath\n";
echo "Table : events\n";
