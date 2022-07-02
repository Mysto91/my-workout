<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Measure;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class MeasureDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $data
     * @param array<string> $context
     *
     * @return boolean
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Measure;
    }

    /**
     * @param mixed $data
     * @param array<string> $context
     *
     * @return Measure
     */
    public function persist($data, array $context = []): Measure
    {
        $data->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param mixed $data
     * @param array<string> $context
     *
     * @return void
     */
    public function remove($data, array $context = []): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
