<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $user = new User();

        $user->setUsername('usuario')
        ->setPassword('$argon2i$v=19$m=65536,t=4,p=1$bFhqZ3NvU3EyMmZNblJ3Sw$DnLscG/3VqvnY4uqGbzO1Q/2ZX8T2U1xKWIA3ePgpIo');
        $manager->persist($user);


        $manager->flush();
    }
}
