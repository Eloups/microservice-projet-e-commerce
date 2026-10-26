<?php

namespace MicroserviceProjetECommerce\Domain\UseCases;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;

/**
 * UseCase pour valider un panier.
 * Consomme les evenements en attente de ce panier, valide le panier
 * en BDD puis publie un evenement de validation.
 */
class ValidateCartUseCase
{
    /**
     * Fonction d'exécution du cas d'utilisation
     * @param int $cartId
     * @param IPortOUTDatabase $databaseAdapter
     * @param IPortOUTEvent $eventAdapter
     * @return Cart
     */
    public function run(int $cartId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        // Validation du panier en BDD (echoue si panier inconnu ou deja valide)
        $cart = $databaseAdapter->validateCart($cartId);

        // Consommation des evenements en attente concernant ce panier :
        // ces changements d'etat sont acquis par la validation
        $consumedEventIds = [];
        foreach ($eventAdapter->getPendingEvents(50) as $event) {
            if (($event['payload']['cartId'] ?? null) === $cartId) {
                $consumedEventIds[] = (int) $event['id'];
            }
        }
        $eventAdapter->markConsumed($consumedEventIds);

        // Annonce de la validation
        $eventAdapter->publish('cart.validated', [
            'cartId' => $cartId,
            'articleCount' => count($cart->getArticles()),
        ]);

        return $cart;
    }
}
