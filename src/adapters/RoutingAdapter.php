<?php

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortINCartCases;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;
use MicroserviceProjetECommerce\Domain\UseCases\AddArticleToCartUseCase;
use MicroserviceProjetECommerce\Domain\UseCases\ValidateCartUseCase;

/**
 * Adaptateur pour connecter le routeur avec le domaine
 */
class RoutingAdapter implements IPortINCartCases
{
    public function runAddArticleToCastCase(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        $useCase = new AddArticleToCartUseCase();
        return $useCase->run($cartId, $articleId, $databaseAdapter, $eventAdapter);
    }

    public function runValidateCartCase(int $cartId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        $useCase = new ValidateCartUseCase();
        return $useCase->run($cartId, $databaseAdapter, $eventAdapter);
    }
}
