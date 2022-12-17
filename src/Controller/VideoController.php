<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Videos;
use App\Form\VideosType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
{
    #[Route('/user/trick/video/{id}/add', name: 'app_user_add_video',methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function addVideo(Trick $trick,Request $request,  EntityManagerInterface $em): Response
    {
        $videos = new Videos;
        $form  = $this->createForm(VideosType::class,$videos);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $video = $form->get('urlVideo')->getData();
            if($video){
               
                //https://www.youtube.com/watch?v=wnr2A4aKnPU
                $regexYoutube = '/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]*)/m';
                $resultYoutube = preg_match($regexYoutube, $video, $matches);

                if ($resultYoutube) {
                    $videos->setTrick($trick);
                    $videos->setPlatform(Videos::YOUTUBE);
                    $videos->setPlatformId($matches[1]);
                    $em->persist($videos);
                    $em->flush();
                    $this->addFlash('success','Vous avez ajouté une video');
                    return $this->redirectToRoute('app_user_show_one',['id'=>$trick->getId()]);
                }
                
                $this->addFlash('success','L \'Url entrée n\'est pas valable,vous devrez entrer une url youtube  ');
            }

        }   

        
    
        return $this->render('video/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/user/trick/video/{trick}/{videos}/update', name: 'app_user_update_video', methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateVideo(Trick $trick,Videos $videos,Request $request,  EntityManagerInterface $em): Response
    {
       
        $form  = $this->createForm(VideosType::class,$videos);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $video = $form->get('urlVideo')->getData();
            if($video){

                
                //https://www.youtube.com/watch?v=wnr2A4aKnPU
                $regexYoutube = '/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]*)/m';
                $resultYoutube = preg_match($regexYoutube, $video, $matches);

                if ($resultYoutube) {
                    $videos->setTrick($trick);
                    $videos->setPlatform(Videos::YOUTUBE);
                    $videos->setPlatformId($matches[1]);
                    $em->flush();
                    $this->addFlash('success','Vous avez modifieé une video');
                    return $this->redirectToRoute('app_user_show_one',['id'=>$trick->getId()]);
                }

               
            }

        }   

        
    
        return $this->render('video/update.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route('/user/trick/{id} - {token}/delete/video', name: 'app_user_trick_delete_video',methods :["GET"])]
    #[IsGranted('ROLE_USER')]
    public function delete(Videos $video, $token,EntityManagerInterface $em):Response 
    {
       
        if($this->isCsrfTokenValid('delete'.$video->getId(),$token)){
           
            $em->remove($video);
            $em->flush();
            $this->addFlash('info','Cette video a été supprimé avec succes');
            
        }
        return $this->redirectToRoute('app_user_trick_show');
      
    }
}
