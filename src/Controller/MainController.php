<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideosRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $Trickrepo,Request $request)
    {
         $page = $request->query->getInt('page', 1);
         $trick = $Trickrepo->trickPaginator($page,4);
        // // $trick =  $Trickrepo->findAll();
        return $this->render('main/index.html.twig',['tricks'=> $trick]);
    }


    #[Route('/trick/{id}-{slug}/show_one', name:"app_trick_show_one")]
    public function show_one(Trick $trick, $slug,TrickRepository $Trickrepo, ImageRepository $imageRepo, VideosRepository $videoRepo,
     Request $request,EntityManagerInterface $em, CommentsRepository $commentRepo)
    {
        $comments = new Comments;
        $form = $this->createForm(CommentsType::class,$comments);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comments->setUser($this->getUser());
            $comments->setTrick($trick);
            $em->persist($comments);
            $em->flush();
        }
        $commentaires = $commentRepo->findBy(['trick'=> $trick],['createdAt'=>'DESC']);
         $trick =  $Trickrepo->findOneBy(['id'=> $trick->getId()]);
         $images = $imageRepo->findBy(['trick'=> $trick]);
         $videos = $videoRepo->findBy(['trick'=> $trick]);
         return $this->render('main/show_trick.html.twig',
        ['trick'=> $trick, 'images'=>$images, 'videos'=>$videos,
        'form'=> $form->createView(),'commentaires'=> $commentaires]);


    }
}
