<?php

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortINCartCases;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Domain\UseCases\AddArticleToCartUseCase;

/**
 * Adaptateur pour connecter le routeur avec le domaine
 */
class RoutingAdapter implements IPortINCartCases
{
    public function runAddArticleToCastCase(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter): Cart
    {
        $useCase = new AddArticleToCartUseCase();
        return $useCase->run($cartId, $articleId, $databaseAdapter);
    }
}
