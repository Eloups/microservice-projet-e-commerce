<?php

namespace Src\Domain\Entities;

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
     * Contructeur de Article
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $price
     */
    public function __construct(int $id, string $name, string $description, float $price) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
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
}