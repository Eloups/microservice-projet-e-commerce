<?php

namespace MicroserviceProjetECommerce\Domain\UseCases;

use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTEvent;

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
     * @param IPortOUTEvent $eventAdapter
     * @return Cart
     */
    public function run(int $cartId, int $articleId, IPortOUTDatabase $databaseAdapter, IPortOUTEvent $eventAdapter): Cart
    {
        $cart = $databaseAdapter->addArticleToCart($cartId, $articleId);

        // Quantite de l'article dans le panier apres l'ajout
        $quantity = 0;
        foreach ($cart->getArticles() as $article) {
            if ($article->getId() === $articleId) {
                $quantity = $article->getQuantity();
            }
        }

        $eventAdapter->publish('cart.article_added', [
            'cartId' => $cartId,
            'articleId' => $articleId,
            'quantity' => $quantity,
        ]);

        return $cart;
    }
}
