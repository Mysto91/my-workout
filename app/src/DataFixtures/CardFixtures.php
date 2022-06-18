<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CardFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=1; $i < 50; $i++) {
            $card = new Card();
            $card->setTitle($faker->name());
            $card->setDescription($faker->text());
            $card->setPoint($faker->randomElement([1, 2, 3, 5, 8, 13, 20]));
            $card->setStartDate($faker->dateTimeBetween());
            $card->setEndDate($faker->dateTimeBetween('+1 year', '+2 year'));
            $manager->persist($card);
        }

        $manager->flush();
    }
}
