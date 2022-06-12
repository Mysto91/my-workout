<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CardFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 50; $i++) {
            $card = new Card();
            $card->setTitle("card n°{$i}");
            $manager->persist($card);
        }

        $manager->flush();
    }
}
