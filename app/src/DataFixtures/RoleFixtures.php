<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ADMIN_ROLE = 'admin';

    public function load(ObjectManager $manager): void
    {
        $roleLabels = [
            'admin',
            'visitor'
        ];

        foreach ($roleLabels as $roleLabel) {
            $role = new Role();
            $role->setLabel($roleLabel);
            if ($roleLabel === 'admin') {
                $this->addReference(self::ADMIN_ROLE, $role);
            }
            $manager->persist($role);
        }

        $manager->flush();
    }
}
