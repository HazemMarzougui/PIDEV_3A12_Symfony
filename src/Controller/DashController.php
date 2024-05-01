<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommandeRepository;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;

class DashController extends AbstractController
{
    #[Route('/dash', name: 'app_dash')]
    public function index(): Response
    {
       // Récupérer les commandes pour l'utilisateur avec l'ID 1
       $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
        
       // Rendre la vue avec les commandes récupérées
       return $this->render('dash/index.html.twig', [
           'controller_name' => 'FrontController',
           'commandes' => $commandes,
       ]);
    }
    #[Route('/Commande/delete/{id}', name: 'dash_commande_delete')]
    public function deleteAuthor(ManagerRegistry $manager,CommandeRepository $repo,$id){
        $commande = $repo->find($id);
        if ($commande){
         

            
        $manager->getManager()->remove($commande);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_dash');
            }
            else {
                return new Response("commande!");
            }
        }
        
      #[Route('/search', name:'search_commande', methods:['GET'])]
      public function search(Request $request, CommandeRepository $commandeRepository): Response
    {
        $query = $request->query->get('query');
        $commandes = $commandeRepository->searchByQuery($query);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }    
}
