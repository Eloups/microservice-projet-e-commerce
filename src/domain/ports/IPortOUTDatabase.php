<?php

namespace MicroserviceProjetECommerce\Domain\Ports;

use MicroserviceProjetECommerce\Domain\Entities\Cart;

interface IPortOUTDatabase {
    /**
     * Ajoute un article au panier en BDD
     * @param int $cartId
     * @param int $articleId
     * @return Cart
     */
    public function addArticleToCart(int $cartId, int $articleId): Cart;

    /**
     * Valide le panier en BDD
     * @param int $cartId
     * @return Cart
     */
    public function validateCart(int $cartId): Cart;
}
