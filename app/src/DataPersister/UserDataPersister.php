<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordEncoder;
    private RoleRepository $roleRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordEncoder, RoleRepository $roleRepository)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param mixed $data
     * @param array<string> $context
     *
     * @return boolean
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param mixed $data
     * @param array<string> $context
     *
     * @return User
     */
    public function persist($data, array $context = []): User
    {
        if ($data->getPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->hashPassword($data, $data->getPassword())
            );
            $data->eraseCredentials();
        }

        /** @phpstan-ignore-next-line */
        $role = $this->roleRepository->findByLabel($data->getRole()->getLabel());

        $data->setRole($role[0]);

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
