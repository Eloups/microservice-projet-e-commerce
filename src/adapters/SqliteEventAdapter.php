<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Database\Sqlite\SqliteEventPDO;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;

/**
 * Adaptateur sortant (driven) : relie le port IPortOUTEvent du domaine
 * a la base SQLite dediee aux evenements via le wrapper SqliteEventPDO.
 */
class SqliteEventAdapter implements IPortOUTEvent
{
    private SqliteEventPDO $database;

    /**
     * @param string|null $dbPath Chemin du fichier SQLite
     *                            (défaut : celui de SqliteEventPDO)
     */
    public function __construct(?string $dbPath = null)
    {
        $this->database = new SqliteEventPDO($dbPath);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(string $name, array $payload): void
    {
        $this->database->publish($name, $payload);
    }
}
