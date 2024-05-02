<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommandeRepository;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        
        #[Route('/dash_commande/search', name: 'app_commande_dash_search', methods: ['GET'])]
        public function search(CommandeRepository $commandeRepository, Request $request): JsonResponse
        {
            $searchTerm = $request->query->get('search');
            $commandes = $commandeRepository->searchByTerm($searchTerm);
            
            $commandesArray = [];
            foreach ($commandes as $commande) {
                $commandesArray[] = [
                    'id' => $commande->getId(),
                    'nom' => $commande->getNom(),
                    'prenom' => $commande->getPrenom(),
                    'adresse' => $commande->getAdresse(),
                    'email' => $commande->getEmail(),
                    'prixTotale' => $commande->getPrixTotale(),
                ];
            }
        
            return new JsonResponse(['commandes' => $commandesArray]);
        }


        #[Route('/dash_commande', name: 'app_commande_dash', methods: ['GET'])]
        public function indexdash(CommandeRepository $commandeRepository,Request $request): Response
        {
            if ($request->isXmlHttpRequest()) {
                // Get the search term from the request
                $searchTerm = $request->query->get('search');
        
                // Call the search function in your repository
                $commandes = $commandeRepository->searchByTerm($searchTerm);
    
        
                // Convert the results to an array
                if($searchTerm == ""){
                    $commandes = $commandeRepository->findAll();;
                }
                $commandesArray = [];
                foreach ($commandes as $commande) {
                    $commandesArray[] = [
                        'id' => $commande->getIdCommande(),
                        'adresse' => $commande->getAdresse(),
                        'numTel' => $commande->getTelephone(),
                        'prix' => $commande->getPrix(),
                        
                    ];
                }
                   
    
                return new JsonResponse(['commandes' => $commandesArray]);
            }
            return $this->render('commande/liste_commande_dash.html.twig', [
                'commandes' => $commandeRepository->findAll(),
            ]);
        }

        #[Route('/art/search', name: 'art_search')]
    public function artSearch(Request $request, CommandeRepository $commandeRepository): Response
    {
        $query = $request->query->get('q');
        $artworks = $commandeRepository->findByTitle($query);
        $artworkCount = count($artworks);
    
        return $this->render('art/search_results.html.twig', [
            'artworks' => $artworks,
            'artworkCount' => $artworkCount,
        ]);
    }
}
