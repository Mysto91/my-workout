<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MeasureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=MeasureRepository::class)
 */
class Measure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $muscleWeight;

    /**
     * @ORM\Column(type="datetime")
     */
    private $measurementDate;

    /**
     * @ORM\Column(type="float")
     */
    private $boneMass;

    /**
     * @ORM\Column(type="float")
     */
    private $bodyWater;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $user;

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
}
