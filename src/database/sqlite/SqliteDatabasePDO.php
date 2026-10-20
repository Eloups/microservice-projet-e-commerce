<?php

declare(strict_types=1);

namespace MicroserviceProjetECommerce\Database\Sqlite;

use PDO;

/**
 * Wrapper PDO d'acces a la base SQLite (infrastructure).
 * Ne connait aucune notion du domaine : retourne des tableaux bruts.
 * Sera appele par un adaptateur qui implementera le port sortant.
 */
class SqliteDatabasePDO
{
    private PDO $pdo;

    /**
     * Ouvre la connexion SQLite, cree le schema si necessaire
     * et insere des donnees de demonstration si la base est vide.
     *
     * @param string|null $dbPath Chemin du fichier SQLite
     *                            (defaut : <racine>/data/ecommerce.sqlite)
     */
    public function __construct(?string $dbPath = null)
    {
        $dbPath ??= dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'ecommerce.sqlite';

        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    /**
     * Connection PDO sous-jacente, si l'adaptateur a besoin
     * d'executer des requetes specifiques.
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Insere un article dans un panier. Sans effet si l'association existe deja.
     */
    public function addArticleToCart(int $cartId, int $articleId): void
    {
        $stmt = $this->pdo->prepare('
            INSERT OR IGNORE INTO cart_articles (cart_id, article_id)
            VALUES (:cartId, :articleId)
        ');
        $stmt->execute(['cartId' => $cartId, 'articleId' => $articleId]);
    }

    /**
     * Ligne du panier avec son utilisateur, ou null si le panier n'existe pas.
     *
     * @return array{id: int, is_validated: int, user_id: int, name: string, firstname: string, email: string|null}|null
     */
    public function getCartWithUser(int $cartId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.id, c.is_validated,
                   u.id AS user_id, u.name, u.firstname, u.email
            FROM carts c
            JOIN users u ON u.id = c.user_id
            WHERE c.id = :cartId
        ');
        $stmt->execute(['cartId' => $cartId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Lignes des articles d'un panier.
     *
     * @return list<array{id: int, name: string, description: string, price: float}>
     */
    public function getArticlesOfCart(int $cartId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT a.id, a.name, a.description, a.price
            FROM articles a
            JOIN cart_articles ca ON ca.article_id = a.id
            WHERE ca.cart_id = :cartId
            ORDER BY a.id
        ');
        $stmt->execute(['cartId' => $cartId]);

        return $stmt->fetchAll();
    }

    /**
     * Verifie l'existence d'un article.
     */
    public function articleExists(int $articleId): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM articles WHERE id = :articleId');
        $stmt->execute(['articleId' => $articleId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Verifie l'existence d'un panier.
     */
    public function cartExists(int $cartId): bool
    {
        return $this->getCartWithUser($cartId) !== null;
    }

    /**
     * Cree les tables si elles n'existent pas.
     * A appeler explicitement (cf. data/init.php).
     */
    public function createSchema(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                name       TEXT NOT NULL,
                firstname  TEXT NOT NULL,
                email      TEXT
            );

            CREATE TABLE IF NOT EXISTS articles (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                name        TEXT NOT NULL,
                description TEXT NOT NULL,
                price       REAL NOT NULL
            );

            CREATE TABLE IF NOT EXISTS carts (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id      INTEGER NOT NULL REFERENCES users(id),
                is_validated INTEGER NOT NULL DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS cart_articles (
                cart_id    INTEGER NOT NULL REFERENCES carts(id),
                article_id INTEGER NOT NULL REFERENCES articles(id),
                PRIMARY KEY (cart_id, article_id)
            );
        ');
    }

    /**
     * Insere des donnees de demonstration si la base est vierge.
     * A appeler explicitement (cf. data/init.php).
     */
    public function seedIfEmpty(): void
    {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $this->pdo->exec("
            INSERT INTO users (name, firstname, email) VALUES
                ('Dupont', 'Alice', 'alice.dupont@example.com'),
                ('Martin',  'Bob',   'bob.martin@example.com');

            INSERT INTO articles (name, description, price) VALUES
                ('Clavier mecanique', 'Clavier azerty switchs rouges', 79.90),
                ('Souris ergonomique', 'Souris verticale sans fil', 54.50),
                ('Ecran 27 pouces', 'Ecran IPS QHD 144 Hz', 249.99);

            INSERT INTO carts (user_id, is_validated) VALUES
                (1, 0),
                (2, 0);
        ");
    }
}
