<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use App\Repository\MeasureRepository;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *      itemOperations={
 *          "get" = {
 *              "security" = "is_granted('MEASURE_READ', object)",
 *              "security_message" = "Access Denied.",
 *           },
 *          "put" = {
 *              "security" = "is_granted('MEASURE_EDIT', object)",
 *              "security_message" = "Access Denied.",
 *           },
 *          "delete" = {
 *              "security" = "is_granted('MEASURE_DELETE', object)",
 *              "security_message" = "Access Denied.",
 *           }
 *      }
 * )
 * @ApiFilter(NumericFilter::class, properties={"user": "exact", "weight": "exact"})
 * @ORM\Entity(repositoryClass=MeasureRepository::class)
 */
class Measure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     */
    private float $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Positive
     */
    private ?float $muscleWeight;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $measurementDate;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     */
    private float $boneMass;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     */
    private float $bodyWater;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getMuscleWeight(): ?float
    {
        return $this->muscleWeight;
    }

    public function setMuscleWeight(?float $muscleWeight): self
    {
        $this->muscleWeight = $muscleWeight;

        return $this;
    }

    public function getMeasurementDate(): ?\DateTimeInterface
    {
        return $this->measurementDate;
    }

    public function setMeasurementDate(\DateTimeInterface $measurementDate): self
    {
        $this->measurementDate = $measurementDate;

        return $this;
    }

    public function getBoneMass(): ?float
    {
        return $this->boneMass;
    }

    public function setBoneMass(float $boneMass): self
    {
        $this->boneMass = $boneMass;

        return $this;
    }

    public function getBodyWater(): ?float
    {
        return $this->bodyWater;
    }

    public function setBodyWater(float $bodyWater): self
    {
        $this->bodyWater = $bodyWater;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
