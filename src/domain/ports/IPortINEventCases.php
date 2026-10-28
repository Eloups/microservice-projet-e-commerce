<?php

namespace MicroserviceProjetECommerce\Domain\Ports;

/**
 * Port entrant pour les cas d'utilisation lies a l'event bus
 * (consommateur externe qui interroge la file).
 */
interface IPortINEventCases {
    /**
     * Fonction pour lancer le UseCase de lecture des evenements en attente
     * @param int $limit
     * @param IPortOUTEvent $eventAdapter
     * @return list<array{id: int, name: string, payload: array<string, mixed>, created_at: string}>
     */
    public function runListPendingEventsCase(int $limit, IPortOUTEvent $eventAdapter): array;
}
