<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Adapters;

use MicroserviceProjetECommerce\Database\Sqlite\SqliteDatabasePDO;
use MicroserviceProjetECommerce\Domain\Entities\Article;
use MicroserviceProjetECommerce\Domain\Entities\Cart;
use MicroserviceProjetECommerce\Domain\Entities\User;
use MicroserviceProjetECommerce\Domain\Errors\CartAlreadyValidatedException;
use MicroserviceProjetECommerce\Domain\Ports\IPortOUTDatabase;
use MicroserviceProjetECommerce\Utils\Logger;
use RuntimeException;

/**
 * Adaptateur sortant (driven) : relie le port IPortOUTDatabase du domaine
 * a la base SQLite via le wrapper SqliteDatabasePDO.
 * Mappe les lignes brutes retournees par le wrapper en entites du domaine.
 */
class SqliteDatabaseAdapter implements IPortOUTDatabase
{
    private SqliteDatabasePDO $database;

    /**
     * @param string|null $dbPath Chemin du fichier SQLite
     *                            (défaut : celui de SqliteDatabasePDO)
     */
    public function __construct(?string $dbPath = null)
    {
        $this->database = new SqliteDatabasePDO($dbPath);
    }

    /**
     * {@inheritdoc}
     */
    public function addArticleToCart(int $cartId, int $articleId): Cart
    {
        $row = $this->database->getCartWithUser($cartId);
        if ($row === null) {
            throw new RuntimeException("Panier inconnu : $cartId");
        }
        if ((bool) $row['is_validated']) {
            throw new CartAlreadyValidatedException($cartId);
        }
        if (!$this->database->articleExists($articleId)) {
            throw new RuntimeException("Article inconnu : $articleId");
        }

        $this->database->addArticleToCart($cartId, $articleId);
        Logger::log('info', 'port OUT Database : article ajoute au panier', ['cartId' => $cartId, 'articleId' => $articleId]);

        return $this->fetchCart($cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCart(int $cartId): Cart
    {
        $row = $this->database->getCartWithUser($cartId);
        if ($row === null) {
            throw new RuntimeException("Panier inconnu : $cartId");
        }
        if ((bool) $row['is_validated']) {
            throw new CartAlreadyValidatedException($cartId);
        }

        $this->database->validateCart($cartId);
        Logger::log('info', 'port OUT Database : panier valide', ['cartId' => $cartId]);

        return $this->fetchCart($cartId);
    }

    /**
     * Reconstruit l'entite Cart (avec son utilisateur et ses articles)
     * a partir des lignes brutes du wrapper PDO.
     */
    private function fetchCart(int $cartId): Cart
    {
        $row = $this->database->getCartWithUser($cartId);

        if ($row === null) {
            throw new RuntimeException("Panier inconnu : $cartId");
        }

        $user = new User(
            (int) $row['user_id'],
            $row['name'],
            $row['firstname'],
            $row['email']
        );

        $articles = [];
        foreach ($this->database->getArticlesOfCart($cartId) as $articleRow) {
            $articles[] = new Article(
                (int) $articleRow['id'],
                $articleRow['name'],
                $articleRow['description'],
                (float) $articleRow['price'],
                (int) $articleRow['quantity']
            );
        }

        return new Cart(
            (int) $row['id'],
            $user,
            (bool) $row['is_validated'],
            $articles
        );
    }
}
