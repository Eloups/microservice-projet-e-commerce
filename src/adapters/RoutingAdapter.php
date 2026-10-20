<?php

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortINCartCases;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;

/**
 * Adaptateur pour connecter le routeur avec le domaine
 */
class RoutingAdapter implements IPortINCartCases {
    public function runAddArticleToCastCase(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter): Cart {
        // TODO
    }

}