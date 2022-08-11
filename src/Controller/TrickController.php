<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\TrickRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class TrickController extends AbstractController
{

    
    #[Route('/', name: 'app_home')]
    public function index( TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([],['createdAt'=>'DESC']);

        //$this->addFlash('success','Bienvenue '.$this->getUser()->getUserIdentifier());

        return $this->render('trick/index.html.twig', ['tricks'=>$tricks]);
    }

    #[Route('/tricks/create', name: 'app_tricks_create',methods :['GET','POST'])]
    public function create(Request $request,EntityManagerInterface $em ,UserInterface $user,SluggerInterface $slugger):Response 
    {
        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de créer un trick');
            return $this->redirectToRoute('app_home');
        }

        $trick = new Trick;
        $form = $this->createForm(TrickType::class,$trick);
           
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('images')->getData();

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

               
                try {
                    $images->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }

                
                $trick->setImages($newFilename);

                
            }
            $trick->setUser($user);
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success','trick créer avec succes');
            return $this->redirectToRoute('app_home');   

        }
        return $this->render('trick/create.html.twig',
        ['formulaire'=>$form->createView()]); 
    }

    #[Route('/tricks/{id<[0-9]+>}/edit', name: 'app_tricks_edit',methods:["GET","PUT","POST"])]
    public function edit(Trick $trick,Request $request,EntityManagerInterface $em,SluggerInterface $slugger):Response 
    {

        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de modifier un trick');
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(TrickType::class,$trick);
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $images = $form->get('images')->getData();

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

                try {
                    $images->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    //.......
                }

               
                $trick->setImages($newFilename);

                
            }
            $em->flush();

            $this->addFlash('success','trick successfully updated');

            return $this->redirectToRoute('app_home');

        }
        return $this->render('trick/edit.html.twig',[
            'formulaire'=>$form->createView(),
            'trick'=>$trick
        ]);
    }

    #[Route('/tricks/{id<[0-9]+>}/delete', name: 'app_tricks_delete',methods :["GET","POST"])]
    public function delete(Trick $trick,Request $request ,EntityManagerInterface $em):Response 
    {
        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de supprimer un trick');
            return $this->redirectToRoute('app_home');
        }
        if($this->isCsrfTokenValid('trick_delete_'.$trick->getId(),$request->request->get('csrf_token'))){
            $em->remove($trick);
            $em->flush();
            $this->addFlash('info','Ce trick a été supprimé avec succes');
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/tricks/{id<[0-9]+>}', name: 'app_tricks_show',methods :['GET','POST'])]
    public function show(Trick $trick,Request $request,EntityManagerInterface $em,CommentsRepository $commentsRepo  ): Response 
    {
        //$trick = $repo->find($id);
        
        if(! $trick){
            throw $this->createNotFoundException("Ce trick n'existe pas ");
        }

        //$user = new User;
        $comments = new Comments;
        $form = $this->createForm(CommentsType::class,$comments);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

             if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter pour pouvoir commenter');
            return $this->redirectToRoute('app_home');
            }

            $comments->setUser($this->getUser());
            $comments->setTrick($trick);
            $em->persist($comments);
            $em->flush();
            

        }
       $emailUser ='defaultEmail';
        if($this->getUser()){
            $emailUser = $this->getUser()->getUserIdentifier();
        }
        
        $emailUser ? $emailUser : 'defaultEmail';
        $allComments = $commentsRepo->findBy( ['trick'=> $trick]);

        return $this->render("trick/show.html.twig",[

            'allComment'=>$allComments,
            'trick'=>$trick,
            'emailUser'=> $emailUser,
            'formulaire'=>$form->createView()
        ]);  
        
    }
}
