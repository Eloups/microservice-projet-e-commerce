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

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'ecommerce.sqlite';

if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "Base existante supprimee : $dbPath\n";
}

// Ouverture de la connexion, creation explicite du schema puis du seed
$db = new SqliteDatabasePDO($dbPath);
$db->createSchema();
$db->seedIfEmpty();

echo "Base creee : $dbPath\n";
echo "Tables : users, articles, carts, cart_articles\n";
echo "Seed insere.\n";
