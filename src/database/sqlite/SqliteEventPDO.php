<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Database\Sqlite;

use PDO;

/**
 * Wrapper PDO d'acces a la base SQLite dediee aux evenements.
 * L'event bus persiste ici les evenements publies pour qu'un
 * consommateur puisse les lire plus tard (table file d'attente).
 * Ne connait aucune notion du domaine : retourne des tableaux bruts.
 */
class SqliteEventPDO
{
    private PDO $pdo;

    /**
     * @param string|null $dbPath Chemin du fichier SQLite
     *                            (défaut : <racine>/data/events.sqlite)
     */
    public function __construct(?string $dbPath = null)
    {
        $dbPath ??= dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'events.sqlite';

        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Connection PDO sous-jacente, si l'adaptateur a besoin
     * d'executer des requetes specifiques.
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Insere un evenement dans la file (payload encode en JSON).
     */
    public function publish(string $name, array $payload): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO events (name, payload)
            VALUES (:name, :payload)
        ');
        $stmt->execute([
            'name' => $name,
            'payload' => json_encode($payload),
        ]);
    }

    /**
     * Les n derniers evenements non consommes, les plus anciens d'abord.
     *
     * @return list<array{id: int, name: string, payload: string, created_at: string}>
     */
    public function getPendingEvents(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare('
            SELECT id, name, payload, created_at
            FROM events
            WHERE consumed_at IS NULL
            ORDER BY id
            LIMIT :limit
        ');
        $stmt->execute(['limit' => $limit]);

        return $stmt->fetchAll();
    }

    /**
     * Marque une liste d'evenements comme consommes.
     *
     * @param int[] $eventIds
     */
    public function markConsumed(array $eventIds): void
    {
        if ($eventIds === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $stmt = $this->pdo->prepare("
            UPDATE events SET consumed_at = datetime('now') WHERE id IN ($placeholders)
        ");
        $stmt->execute($eventIds);
    }

    /**
     * Cree la table des evenements si elle n'existe pas.
     * A appeler explicitement (cf. data/init.php).
     */
    public function createSchema(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS events (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                name        TEXT NOT NULL,
                payload     TEXT NOT NULL,
                created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                consumed_at TEXT
            );

            CREATE INDEX IF NOT EXISTS idx_events_pending
                ON events (id) WHERE consumed_at IS NULL;
        ');
    }
}
