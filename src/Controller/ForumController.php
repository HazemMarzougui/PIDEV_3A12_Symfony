<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }
    //List The Forums
    #[Route('/forumlist',name:'app_list_forums')]
    public function getAll(ForumRepository $repo){
        $forums = $repo->findAll();
        return $this->render('forum/display.html.twig',[
            'forums'=>$forums
        ]);
    }
    //Add a Forum
    #[Route('/forumadd',name:'app_add_forum')]
    public function AddForum(Request $req,ManagerRegistry $manager){
        $Newforum = new Forum();
        $form = $this->createForm(ForumType::class,$Newforum);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $Newforum->setRepliesNumber(0);
            $today = new DateTime();
            $Newforum->setDate($today);
            $manager->getManager()->persist($Newforum);
            $manager->getManager()->flush();
            return $this->redirectToRoute('app_list_forums');
        }
        return $this->render('forum/add.html.twig',['f'=>$form->createView()]);
    }
    //Delete the Forums
    #[Route('/forum/delete/{id}',name:'app_delete_forum')]
    public function delete($id,ManagerRegistry $manager,ForumRepository $repo){
        $forum = $repo->find($id);
        $manager->getManager()->remove($forum);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_list_forums');
    }
    //Update the Forum
    #[Route('/forumupdate{id}',name:'app_update_author')]
    public function update(ForumRepository $rep,$id,Request $req,ManagerRegistry $manager){
        $forum = $rep->find($id);
        $form = $this->createForm(ForumType::class,$forum);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $manager->getManager()->persist($forum);
            $manager->getManager()->flush();
        return $this->redirectToRoute('app_list_forums');
        }
        return $this->render('forum/add.html.twig',['f'=>$form->createView()]);
    }
}
