<?php

declare(strict_types=1);

/**
 * Simulation de panne : pose un verrou EXCLUSIF sur la base metier
 * (data/ecommerce.sqlite). Tant que ce script tourne, toute requete
 * vers la base echoue avec "database is locked" -> le service repond 503.
 *
 * Arreter avec Ctrl+C : la transaction est annulee et le verrou libere,
 * le service repart sans perte de donnees.
 *
 * Usage : composer run panne-on   (ou php data/panne-on.php)
 */

require __DIR__ . '/../vendor/autoload.php';

use MicroserviceProjetECommerce\Database\Sqlite\SqliteDatabasePDO;

$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'ecommerce.sqlite';

if (!file_exists($dbPath)) {
    echo "Base introuvable : $dbPath (lancez composer run db-init avant)\n";
    exit(1);
}

$pdo = (new SqliteDatabasePDO($dbPath))->getPDO();
$pdo->exec('BEGIN EXCLUSIVE');

echo "PANNE ACTIVE\n";
echo "Verrou exclusif pose sur : $dbPath\n";
echo "Toute requete metier echoue (database is locked -> HTTP 503).\n";
echo "La base evenements (events.sqlite) reste operationnelle.\n";
echo "Ctrl+C pour lever la panne.\n";

// Boucle d'attente : le verrou reste tenu tant que le processus vit
while (true) {
    sleep(1);
}
