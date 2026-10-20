<?php

namespace MicroserviceProjetECommerce\Domain\UseCases;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;

/**
 * UseCase pour ajouter un article au panier
 */
class AddArticleToCartUseCase
{
    /**
     * Fonction d'exécution du cas d'utilisation
     * @param int $cartId
     * @param int $articleId
     * @param IPortOUTDatabase $databaseAdapter
     * @return Cart
     */
    public function run(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter): Cart
    {
        return $databaseAdapter->addArticleToCart($cartId, $articleId);
    }
}
