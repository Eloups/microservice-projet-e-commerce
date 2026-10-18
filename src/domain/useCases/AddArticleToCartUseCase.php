<?php

namespace Src\Domain\UseCases;

use Src\Domain\Entities\Cart;
use Src\Domain\Ports\IPortOUTDatabase;

/**
 * UseCase pour ajouter un article au panier
 */
class AddArticleToCartUseCase {
    public function run(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter): Cart {
        $databaseAdapter->addArticleToCart($cartId, $articleId);
    }
}