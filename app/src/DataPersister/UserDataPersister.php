<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserDataPersister implements DataPersisterInterface
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

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist($data): User
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

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    public function remove($data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
