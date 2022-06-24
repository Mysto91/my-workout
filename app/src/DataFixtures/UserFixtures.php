<?php

namespace App\DataFixtures;

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

        $adminUser = new User();

        $adminUser->setEmail($faker->email())
            ->setUsername('admin')
            ->setPassword($this->userPasswordEncoder->hashPassword($adminUser, 'admin'))
            ->setName($faker->name())
            ->setFirstname($faker->firstName())
            ->setRole($this->getReference(RoleFixtures::ADMIN_ROLE));

        $manager->persist($adminUser);

        $visitorUser = new User();

        $visitorUser->setEmail($faker->email())
            ->setUsername('admin')
            ->setPassword($this->userPasswordEncoder->hashPassword($visitorUser, 'visitor'))
            ->setName($faker->name())
            ->setFirstname($faker->firstName())
            ->setRole($this->getReference(RoleFixtures::VISITOR_ROLE));

        $manager->persist($visitorUser);

        $manager->flush();
    }
}
