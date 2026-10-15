<?php

namespace Src\Domain\Entities;

/**
 * Classe des utilisateurs
 */
class User {
    /**
     * ID unique
     * @var int
     */
    private int $id;
    /**
     * Nom d'utilisateur
     * @var string
     */
    private string $name;
    /**
     * Prénom d'utilisateur
     * @var string
     */
    private string $firstname;
    /**
     * Email d'utilisateur
     * @var string
     */
    private ?string $email;

    /**
     * Constructeur de User
     * @param int $id
     * @param string $name
     * @param string $firstname
     * @param mixed $email
     */
    public function __construct(int $id, string $name, string $firstname, ?string $email) {
        $this->id = $id;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->email = $email;
    }

    /**
     * Getter de l'ID
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Getter du Nom
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Getter du prénom
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * Getter de l'email
     * @return string
     */
    public function getEmail(): ?string {
        return $this->email;
    }
}