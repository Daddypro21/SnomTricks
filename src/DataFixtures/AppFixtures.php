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
        $user->setFirstName('Joshua');
        $user->setLastName('Joshua');
        $user->setEmail('Joshua@net.com');
        $password = $this->hasher->hashPassword($user,'123456');
        $user->setPassword( $password );
         $manager->persist($user);
        for ($i = 0; $i < 6; $i++) {

            $trick = new Trick();
            $trick->setTitle('trick fake'.$i);
            $trick->setImages('fake.jpg');
            $trick->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad');
            $trick->setUser($user);
            $manager->persist($trick);
            
        }
        
        $manager->flush();
       
    }
}
