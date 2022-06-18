<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordEncoder;

    public function __construct(UserPasswordHasherInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();

        $user->setEmail($faker->email())
            ->setUsername('admin')
            ->setPassword($this->userPasswordEncoder->hashPassword($user, 'admin'))
            ->setName($faker->name())
            ->setFirstname($faker->firstName())
            ->setRole($this->getReference(RoleFixtures::ADMIN_ROLE));

        $manager->persist($user);

        $manager->flush();
    }
}
