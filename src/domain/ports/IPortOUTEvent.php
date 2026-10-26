<?php

namespace MicroserviceProjetECommerce\Domain\Ports;

interface IPortOUTEvent {
    /**
     * Publie un evenement nomme avec sa charge utile
     * @param string $name
     * @param array<string, mixed> $payload
     */
    public function publish(string $name, array $payload): void;
}
