<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Utilisateur;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\StripeService;
use Stripe\Charge;
use Stripe\Stripe;



#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/new', name: 'app_checkout')]
    public function new(Request $request, EntityManagerInterface $entityManager,ProduitRepository $rep): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        $cartProducts = $this->get('session')->get('cart_products', []);
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
             $cartProducts = $this->get('session')->get('cart_products', []);
            $entityManager->persist($commande);
            $entityManager->flush();

            // Rediriger vers une page de confirmation ou une autre page
            $this->get('session')->set('cart_products', []);
            return $this->redirectToRoute('app_show');
        }
        
        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'cartProducts' => $cartProducts,
            
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
    
    #[Route('/updateCommande/{id}', name: 'app_Commande_update')]
    public function updateBook($id,CommandeRepository $repo,Request $req,ManagerRegistry $manager){
        $author =$repo->find($id);
        $form = $this->createForm(CommandeType::class,$author);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_show');
        }
        return $this->render('commande/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/Commande/delete/{id}', name: 'app_commande_delete')]
    public function deleteAuthor(ManagerRegistry $manager,CommandeRepository $repo,$id){
        $author = $repo->find($id);
        if ($author){
         

            
        $manager->getManager()->remove($author);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_show');
            }
            else {
                return new Response("commande!");
            }
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
                        'id' => $commande->getId(),
                        'nom' => $commande->getNom(),
                        'prenom' => $commande->getPrenom(),
                        'adresse' => $commande->getAdresse(),
                        'email' => $commande->getEmail(),
                    ];
                }
    
                return new JsonResponse(['commande' => $commandesArray]);
            }
            return $this->render('dash/index.html.twig', [
                'commandes' => $commandeRepository->findAll(),
            ]);
        }



        #[Route('/payment/create-charge', name: 'app_stripe_charge2', methods: ['POST'])]
        public function createCharge(Request $request)
        {
            Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
            Charge::create ([
                    "amount" => 5 * 100,
                    "currency" => "usd",
                    "source" => $request->request->get('stripeToken'),
                    "description" => "Binaryboxtuts Payment Test"
            ]);
            $this->addFlash(
                'success',
                'Payment Successful!'
            );
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }       
                

}
