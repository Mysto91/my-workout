<?php

namespace App\DataFixtures;

use App\Entity\Measure;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MeasureFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 50; $i++) {
            $measure = new Measure();
            $measure
                ->setWeight($faker->randomFloat(2, 60, 70))
                ->setMuscleWeight($faker->randomFloat(2, 50, 60))
                ->setMeasurementDate($faker->dateTimeBetween('-1 year', '-1 day'))
                ->setBoneMass($faker->randomFloat(2, 5, 10))
                ->setBodyWater($faker->randomFloat(2, 50, 60))
                ->setUser($faker->randomElement($this->userRepository->findAll()))
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt($faker->dateTime())
            ;

            $manager->persist($measure);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
