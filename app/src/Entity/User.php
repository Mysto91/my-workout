<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      normalizationContext={
 *          "groups"={"read:collection"}
 *      },
 *      itemOperations={
 *          "get" = {
 *              "security" = "is_granted('USER_READ', object)",
 *              "security_message" = "Access Denied.",
 *           },
 *          "put" = {
 *              "security" = "is_granted('USER_EDIT', object)",
 *              "security_message" = "Access Denied.",
 *           },
 *          "delete" = {
 *              "security" = "is_granted('USER_DELETE', object)",
 *              "security_message" = "Access Denied.",
 *           }
 *      }
 * )
 * @UniqueEntity(
 *      "email",
 *      message = "The email already exists."
 * )
 * @UniqueEntity(
 *      "username",
 *      message = "The username already exists."
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $apiKey;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"read:collection"})
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private string $password;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class)
     * @Assert\NotNull
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Role $role = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        $role = strtoupper($this->role->getLabel());
        return ["ROLE_{$role}"];
    }

    public function getSalt(): ?string
    {
        return '';
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
