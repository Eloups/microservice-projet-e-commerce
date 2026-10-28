<?php

namespace MicroserviceProjetECommerce\Domain\Entities;

/**
 * Entitée Article
 */
class Article {
    /**
     * ID unique
     * @var int
     */
    private int $id;
    /**
     * Nom de l'article
     * @var string
     */
    private string $name;
    /**
     * Description de l'article
     * @var string
     */
    private string $description;
    /**
     * Prix de l'article
     * @var float
     */
    private float $price;
    /**
     * Quantite de l'article dans un panier
     * @var int
     */
    private int $quantity;

    /**
     * Contructeur de Article
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $price
     * @param int $quantity
     */
    public function __construct(int $id, string $name, string $description, float $price, int $quantity = 1) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    /**
     * Getter de l'ID
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Getter du nom
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Getter de la description
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Getter du prix
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * Getter de la quantite
     * @return int
     */
    public function getQuantity(): int {
        return $this->quantity;
    }
}