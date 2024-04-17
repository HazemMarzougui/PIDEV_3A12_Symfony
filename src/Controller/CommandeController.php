<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;



#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/new', name: 'app_checkout')]
    public function new(Request $request, EntityManagerInterface $entityManager,ProduitRepository $rep): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


        $timestamp = time();
        $uniqueInt = $timestamp % (10 ** 9); 

            $commande->setIdcommande($uniqueInt);
            $cartProducts = $this->get('session')->get('cart_products', []);
            $quantitiesDataJSON = $request->query->get('quantities');
            $quantitiesData = json_decode($quantitiesDataJSON, true);
               foreach ($cartProducts as $index => $item) {
                $panier = new Panier();
                $panier->setIdCommande($uniqueInt);
                $productId = $item['productId'];
                $quantity = isset($quantitiesData[$index]) ? $quantitiesData[$index]['quantity'] : 1;
                $produit = $rep->find($productId);
                if ($produit) {
                    $panier->setIdProduit($produit);
                    $panier->setPrixU($produit->getPrix()); 
                    $panier->setQuantite($quantity); 
                    $entityManager->persist($panier);
                    $entityManager->flush();

                }
            }
            $entityManager->persist($commande);
            $entityManager->flush();

            // Rediriger vers une page de confirmation ou une autre page
            $this->get('session')->set('cart_products', []);
            return $this->redirectToRoute('app_show');
        }
        
        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/show', name: 'app_show')]
    public function index(): Response
    {
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();

        return $this->render('commande/show.html.twig', [
            'controller_name' => 'FrontController',
            'commandes' => $commandes,
        ]);
    }    
}
