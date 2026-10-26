<?php

namespace MicroserviceProjetECommerce\Domain\Ports;

interface IPortOUTEvent {
    /**
     * Publie un evenement nomme avec sa charge utile
     * @param string $name
     * @param array<string, mixed> $payload
     */
    public function publish(string $name, array $payload): void;

    /**
     * Les evenements en attente (payload deja dekode), les plus anciens d'abord
     * @param int $limit
     * @return list<array{id: int, name: string, payload: array<string, mixed>, created_at: string}>
     */
    public function getPendingEvents(int $limit = 10): array;

    /**
     * Marque des evenements comme consommes
     * @param int[] $eventIds
     */
    public function markConsumed(array $eventIds): void;
}
