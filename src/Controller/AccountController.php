<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use App\Form\ChangePasswordFormType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/edit', name: 'app_account_edit', methods:['POST','GET'])]
    public function edit(Request $request, EntityManagerInterface $em ):Response 
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash('success','Compte modifié avec succes');

            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/account/change-password', name: 'app_account_changepassword', methods:['POST','GET'])]
    public function changePassword( Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $userPasswordHasher): Response 
    {
        
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($userPasswordHasher->hashPassword( $user,$form->get('plainPassword')->getData()));
            $em->flush();

            $this->addFlash('success',' mot de passe mis à jour avec succès');

            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/changepassword.html.twig', [
            'formulaire' => $form->createView()
        ]);

    }

    #[Route('/account/show-account/{id<[0-9]+>}', name: 'app_account_show', methods:['POST','GET'])]
    public function showAccount(User $user,Request $request, 
    SluggerInterface $slugger, UserRepository $userRepo, 
    TrickRepository $trickRepo , ImageRepository $imageRepo)
    {
        $userInfo=  $userRepo->findOneBy(['id'=> $user]);
        $tricks = $trickRepo->findBy(['user'=> $userInfo]);
        $images = $imageRepo->findAll(['trick'=> $tricks]);
         return $this->render('main/show_user_info.html.twig',
        ['user_info' =>  $userInfo ,'images'=> $images]);
    }
}
