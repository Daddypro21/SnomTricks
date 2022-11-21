<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Entity\Comments;
use App\Entity\Imageupdate;
use App\Form\CommentsType;
use App\Form\UpdateimageType;
use App\Service\UploaderService;
use App\Repository\TrickRepository;
use App\Repository\ImagesRepository;
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
    public function create(Request $request,EntityManagerInterface $em ,UserInterface $user ,UploaderService $uploaderService,TrickRepository $trickRepository,SluggerInterface $slugger):Response 
    {
        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de créer un trick');
            return $this->redirectToRoute('app_home');
        }

        $trick = new Trick;
        $form = $this->createForm(TrickType::class,$trick);
           
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cover = $form->get('cover')->getData();

            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();

               
                try {
                    $cover->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }

             
            $trick->setCover($newFilename);
            $trick->setSlug($slugger->slug($trick->getTitle()));
            $trick->setUser($user);
            $trickRepository->add($trick, true); 
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success','trick créer avec succes');
            return $this->redirectToRoute('app_home');   

        }  
         
        }
            return $this->render('trick/create.html.twig',
            [
                'trick'=> $trick,
                'formulaire'=>$form->createView()
            ]);
    }

    #[Route('/tricks/{id<[0-9]+>}/edit', name: 'app_tricks_edit',methods:["GET","PUT","POST"])]
    public function edit(Trick $trick,Request $request,EntityManagerInterface $em,UserInterface $user,SluggerInterface $slugger,UploaderService $uploaderService,TrickRepository $trickRepository):Response 
    {

        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de modifier un trick');
            return $this->redirectToRoute('app_home');
        }
        $form = $this->createForm(TrickType::class,$trick);
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cover = $form->get('cover')->getData();

            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();

               
                try {
                    $cover->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }

             
            $trick->setCover($newFilename);
            $trick->setSlug($slugger->slug($trick->getTitle(),'_'));
            $trick->setUser($user);
            $trickRepository->add($trick, true); 
            $em->flush();
            $this->addFlash('success','trick modifié avec succes');
            return $this->redirectToRoute('app_home');   

        }  
         
        }
            return $this->render('trick/edit.html.twig',
            [
                'trick'=> $trick,
                'formulaire'=>$form->createView()
            ]);
    }


    #[Route('/tricks/{id<[0-9]+>}/modif', name: 'app_tricks_modif',methods:["GET","PUT","POST"])]
    public function editOne( $id,Request $request,EntityManagerInterface $em,UserInterface $user,SluggerInterface $slugger,UploaderService $uploaderService,TrickRepository $trickRepository):Response 
    {

        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de modifier un trick');
            return $this->redirectToRoute('app_home');
        }
        $image = new Imageupdate();
        $form = $this->createForm(UpdateimageType::class,$image);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get('imageupdate')->getData();
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
            try {
                $image->move(
                    $this->getParameter('image_update'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ...
            }

            $q = $em->createQueryBuilder()
                ->update('App\Entity\Images', 'u')
                ->set('u.filename', ':filename')
                ->where('u.id = :id')
                ->setParameter('id', $id)
                ->setParameter('filename', $newFilename)
                ->getQuery();
            $p = $q->execute();

            $this->addFlash('success','image modifié avec succes');
            return $this->redirectToRoute('app_home');
        }
        
        
        return $this->render('trick/edit_one.html.twig',
            [
                'form'=> $form->createView()
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

    #[Route('/tricks/{image}-{id<[0-9]+>}/supp', name: 'app_tricks_supp',methods :["GET","POST"])]
    public function supprimer($image,Trick $trick, Request $request ,ImagesRepository $imageRepo,EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('info','Vous devrez vous connecter avant de supprimer un trick');
            return $this->redirectToRoute('app_home');
        }
        if($this->isCsrfTokenValid('trick_delete_'.$trick->getId(),$request->request->get('csrf_token'))){
        $query = $em->createQuery("DELETE FROM App\Entity\Images e WHERE e.id = " .$image );
        $query->execute();
        $this->addFlash('info','image supprimé avec succes');
        return $this->redirectToRoute('app_home');
        }
    }


    #[Route('/tricks/{slug}-{id<[0-9]+>}', name: 'app_tricks_show',methods :['GET','POST'])]
    public function show($slug,Trick $trick,Request $request,EntityManagerInterface $em,CommentsRepository $commentsRepo  ): Response 
    {
        
        if(! $trick){
            throw $this->createNotFoundException("Ce trick n'existe pas ");
        }
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
            
            return $this->redirectToRoute('app_tricks_show',['id'=>$trick->getId()]);
        }
       $emailUser ='defaultEmail';
        if($this->getUser()){
            $emailUser = $this->getUser()->getUserIdentifier();
        }
        
        $emailUser ? $emailUser : 'defaultEmail';
        $allComments = $commentsRepo->findBy( ['trick'=> $trick],['createdAt'=>'DESC']);

        return $this->render("trick/show.html.twig",[

            'allComment'=>$allComments,
            'trick'=>$trick,
            'emailUser'=> $emailUser,
            'formulaire'=>$form->createView()
        ]);  
        
    }
}
