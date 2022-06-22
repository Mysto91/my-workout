<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ADMIN_ROLE = 'admin';
    public const VISITOR_ROLE = 'visitor';

    public function load(ObjectManager $manager): void
    {
        $roleLabels = [
            self::ADMIN_ROLE,
            self::VISITOR_ROLE
        ];

        foreach ($roleLabels as $roleLabel) {
            $role = new Role();
            $role->setLabel($roleLabel);
            $this->addReference($roleLabel, $role);
            $manager->persist($role);
        }

        $manager->flush();
    }
}
