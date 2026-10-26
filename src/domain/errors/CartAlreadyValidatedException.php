<?php

namespace MicroserviceProjetECommerce\Domain\Errors;

use RuntimeException;

/**
 * Erreur metier levee lorsqu'on tente de modifier ou de valider
 * un panier deja valide.
 */
class CartAlreadyValidatedException extends RuntimeException
{
    public function __construct(int $cartId)
    {
        parent::__construct("Panier deja valide : $cartId");
    }
}
