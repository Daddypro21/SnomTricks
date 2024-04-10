<?php

namespace App\Controller;


use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class TrickController extends AbstractController
{

    #[Route('/user/trick/create', name: 'app_user_trick_create')]
    #[IsGranted('ROLE_USER')]
    public function create( Request $request, EntityManagerInterface $em,SluggerInterface $slugger): Response
    {

        $trick = new Trick;
        $form = $this->createForm( TrickType::class, $trick);
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

                $trick->setUser($this->getUser());
                $trick->setSlug($trick->getTitle());
                $trick->setCover($newFilename);
                $em->persist($trick);
                $em->flush();

                $this->addFlash('success','Vous avez créé un nouveau trick,vous pouvez ajouter des images et videos');
                return $this->redirectToRoute('app_user_trick_show');
            }

            
        }

      
        return $this->render('trick/create.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    
    #[Route('/user/trick/edit/{id<[0-9]+>}', name: 'app_user_trick_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Trick $trick,Request $request, EntityManagerInterface $em,SluggerInterface $slugger)
    {
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

                $trick->setUser($this->getUser());
                $trick->setSlug($trick->getTitle());
                $trick->setCover($newFilename);
                $em->flush();

                $this->addFlash('success','Vous avez modifié le trick');
                return $this->redirectToRoute('app_user_show_one',['id'=>$trick->getId()]);
            }
        }

        return $this->render('trick/edit.html.twig', [
            'form'=> $form->createView(),
        ]);

    }

    #[Route('/user/trick/show', name:"app_user_trick_show")]
    #[IsGranted('ROLE_USER')]
    public function shows(TrickRepository $Trickrepo)
    {
        $trick =  $Trickrepo->findBy(['user'=> $this->getUser()],['createdAt'=>'DESC']);
        return $this->render('trick/show_tricks.html.twig',['tricks'=> $trick]);
    }


    #[Route('/user/trick/{id<[0-9]+>}/show_one', name:"app_user_show_one")]
    #[IsGranted('ROLE_USER')]
    public function show_one(Trick $trick ,TrickRepository $Trickrepo, ImageRepository $imageRepo, VideosRepository $videoRepo)
    {
        if(!$trick){
            return $this->redirectToRoute('app_home');
        }
        $trick =  $Trickrepo->findOneBy(['id'=> $trick->getId()]);
        $images = $imageRepo->findBy(['trick'=> $trick]);
        $videos = $videoRepo->findBy(['trick'=> $trick]);

        return $this->render('trick/show_one_trick.html.twig',
        ['trick'=> $trick, 'images'=>$images, 'videos'=> $videos]);


    }
    #[Route('/user/trick/{id<[0-9]+>} - {token}/delete', name: 'app_user_trick_delete',methods :["GET"])]
    #[IsGranted('ROLE_USER')]
    public function delete(Trick $trick, $token,EntityManagerInterface $em):Response 
    {
       
        if($this->isCsrfTokenValid('delete'.$trick->getId(),$token)){
           
            $em->remove($trick);
            $em->flush();
            $this->addFlash('success','Ce trick a été supprimé avec succes');
            
        }
        return $this->redirectToRoute('app_user_trick_show');
      
    }
}
