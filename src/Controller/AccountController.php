<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
