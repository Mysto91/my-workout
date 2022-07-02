<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $userPasswordEncoder;
    private int $userAdminId = 1;
    private int $userVisitorId = 2;

    public function __construct(UserPasswordHasherInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $adminUser = new User();

        $adminUser
            ->setId($this->userAdminId)
            ->setEmail($faker->email())
            ->setUsername('admin')
            ->setPassword($this->userPasswordEncoder->hashPassword($adminUser, 'admin'))
            ->setName($faker->name())
            ->setFirstname($faker->firstName())
            ->setRole($this->getReference(RoleFixtures::ADMIN_ROLE))
            ->setCreatedAt(new DateTimeImmutable())
            ;

        $manager->persist($adminUser);

        $userId = $this->userVisitorId;

        for ($i = 0; $i < 3; $i++) {
            $visitorUser = new User();

            $visitorUser
                ->setId($userId)
                ->setEmail("visitor_{$userId}@visitor.com")
                ->setUsername("visitor_{$userId}")
                ->setPassword($this->userPasswordEncoder->hashPassword($visitorUser, 'visitor'))
                ->setName($faker->name())
                ->setFirstname($faker->firstName())
                ->setRole($this->getReference(RoleFixtures::VISITOR_ROLE))
                ->setCreatedAt(new DateTimeImmutable())
                ;

            $manager->persist($visitorUser);

            $userId++;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class
        ];
    }
}
