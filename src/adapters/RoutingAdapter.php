<?php

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortINCartCases;
use MicroserviceProjetECommerce\Domain\Ports\IPortINEventCases;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;
use MicroserviceProjetECommerce\Domain\UseCases\AddArticleToCartUseCase;
use MicroserviceProjetECommerce\Domain\UseCases\ListPendingEventsUseCase;
use MicroserviceProjetECommerce\Domain\UseCases\ValidateCartUseCase;
use MicroserviceProjetECommerce\Utils\Logger;

/**
 * Adaptateur pour connecter le routeur avec le domaine
 */
class RoutingAdapter implements IPortINCartCases, IPortINEventCases
{
    public function runAddArticleToCastCase(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        Logger::log('info', 'port IN : use case AddArticleToCart', ['cartId' => $cartId, 'articleId' => $articleId]);

        $useCase = new AddArticleToCartUseCase();
        return $useCase->run($cartId, $articleId, $databaseAdapter, $eventAdapter);
    }

    public function runValidateCartCase(int $cartId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        Logger::log('info', 'port IN : use case ValidateCart', ['cartId' => $cartId]);

        $useCase = new ValidateCartUseCase();
        return $useCase->run($cartId, $databaseAdapter, $eventAdapter);
    }

    public function runListPendingEventsCase(int $limit, IPortOUTEvent $eventAdapter): array
    {
        Logger::log('info', 'port IN : use case ListPendingEvents', ['limit' => $limit]);

        $useCase = new ListPendingEventsUseCase();
        return $useCase->run($limit, $eventAdapter);
    }
}
