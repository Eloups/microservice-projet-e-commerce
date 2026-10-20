<?php

namespace MicroserviceProjetECommerce\Domain\Ports;

use MicroserviceProjetECommerce\Domain\Entities\Cart;

interface IPortINCartCases {
    /**
     * Fonction pour lancer le UseCase d'ajout d'un article au panier
     * @param int $cartId
     * @param int $articleId
     * @param IPortOUTDatabase $databaseAdapter
     * @return Cart
     */
    public function runAddArticleToCastCase(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter): Cart;
}