<?php

namespace Src\Domain\Ports;

use Src\Domain\Entities\Cart;

interface IPortOUTDatabase {
    /**
     * Ajoute un article au panier en BDD
     * @param int $cartId
     * @param int $articleId
     * @return Cart
     */
    public function addArticleToCart(int $cartId, int $articleId): Cart;
}