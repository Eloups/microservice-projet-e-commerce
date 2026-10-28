<?php

namespace MicroserviceProjetECommerce\Domain\UseCases;

use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;

/**
 * UseCase pour lister les evenements en attente de l'event bus.
 * Sert au consommateur externe pour consulter la file.
 */
class ListPendingEventsUseCase
{
    /**
     * Fonction d'exécution du cas d'utilisation
     * @param int $limit
     * @param IPortOUTEvent $eventAdapter
     * @return list<array{id: int, name: string, payload: array<string, mixed>, created_at: string}>
     */
    public function run(int $limit, IPortOUTEvent $eventAdapter): array
    {
        return $eventAdapter->getPendingEvents($limit);
    }
}
