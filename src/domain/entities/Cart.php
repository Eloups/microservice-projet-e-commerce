<?php

namespace MicroserviceProjetECommerce\Domain\Entities;

/**
 * Classe des paniers
 */
class Cart {
    /**
     * ID unique du panier
     * @var int
     */
    private int $id;

    /**
     * Utilisateur lié au panier
     * @var User
     */
    private User $user;

    /**
     * Vrai si panier validé sinon faux
     */
    private bool $isValidate;

    /**
     * Tableau des articles dans le panier
     * @var array
     */
    private array $articles;

    /**
     * Constructeur de Cart
     * @param int $id
     * @param User $user
     * @param bool $isValidate
     * @param array[Article]|null $articles
     */
    public function __construct(int $id, User $user, bool $isValidate, ?array $articles = null) {
        $this->id = $id;
        $this->user = $user;
        $this->isValidate = $isValidate;
        $this->articles = $articles ?? [];
    }

    /**
     * Getter de l'ID
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }
    
    /**
     * Getter de l'utilisateur
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * Getter de si le panier est validé
     * @return bool
     */
    public function getIsValidate(): bool {
        return $this->isValidate;
    }

    /**
     * Getter des articles
     * @return array[Article]
     */
    public function getArticles(): array {
        return $this->articles;
    }
}