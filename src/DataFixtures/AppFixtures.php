<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setFirstName('Morgan');
        $user->setLastName('Morgan');
        $user->setEmail('morgan@net.com');
       

        $password = $this->hasher->hashPassword($user,'123456');
        $user->setPassword( $password );
        
            $trick = new Trick();
            $trick->setTitle('trick fake');
            $trick->setImages('fake1.jpg');
            $trick->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad');
            $trick->getUser();

        
        
        $manager->persist($user);
        $manager->flush();
    }
}
