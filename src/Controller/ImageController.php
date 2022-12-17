<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageController extends AbstractController
{
    #[Route('/user/trick/{id}/image/add', name: 'app_user_add_image',methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function add( Trick $trick , Request $request,EntityManagerInterface $em,SluggerInterface $slugger): Response
    {
        $images = new Image;
        $form = $this->createForm(ImageType::class,$images);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get('filename')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

               
                try {
                    $image->move(
                        $this->getParameter('images_trick_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }
                
                $images->setTrick($trick);
                $images->setFilename($newFilename);
                $em->persist($images);
                $em->flush();

                $this->addFlash('success','Vous avez ajouté une image');
                return $this->redirectToRoute('app_user_show_one',['id'=>$trick->getId()]);
            }
        }
        return $this->render('image/add.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route('/user/trick/{trick}/{images}/image/edit', name: 'app_user_edit_image',methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit( Trick $trick,Image $images , Request $request,EntityManagerInterface $em,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ImageType::class,$images);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get('filename')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

               
                try {
                    $image->move(
                        $this->getParameter('images_trick_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }
                
                $images->setTrick($trick);
                $images->setFilename($newFilename);
                $em->flush();

                $this->addFlash('success','Vous avez modifié une image');
                return $this->redirectToRoute('app_user_show_one',['id'=>$trick->getId()]);
            }
        }
        return $this->render('image/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/user/trick/{id<[0-9]+>} - {token}/delete/image', name: 'app_user_trick_delete_image',methods :["GET"])]
    #[IsGranted('ROLE_USER')]
    public function delete(Image $image, $token,EntityManagerInterface $em):Response 
    {
       
        if($this->isCsrfTokenValid('delete'.$image->getId(),$token)){
           
            $em->remove($image);
            $em->flush();
            $this->addFlash('info','Ce trick a été supprimé avec succes');
            
        }
        return $this->redirectToRoute('app_user_trick_show');
      
    }
}
