<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Produit;

#[Route('/panier')]
class PanierController extends AbstractController
{
 #[Route('/cart', name: 'app_cart')]
public function index(): Response
{
    // Supposons que vous stockiez les produits ajoutés au panier dans une variable de session
    $cartProducts = $this->get('session')->get('cart_products', []);

    // Calculez le prix total du panier en additionnant les prix de chaque produit
    $cartTotal = 0;
    foreach ($cartProducts as $product) {
        $cartTotal += $product['productPrice']; // Supposons que le prix du produit soit stocké dans 'productPrice'
    }

    return $this->render('panier/index.html.twig', [
        'cartProducts' => $cartProducts,
        'cartTotal' => $cartTotal, // Transmettez le prix total au template Twig

    ]);
}

    
    #[Route('/add_to_cart', name: 'add_to_cart_route', methods: ['POST'])]
    public function addToCart(): Response
    {
        // Récupérer les données du formulaire envoyées en POST
        $productId = $this->get('request_stack')->getCurrentRequest()->request->get('productId');
        $quantity = $this->get('request_stack')->getCurrentRequest()->request->get('quantite');
        $productName = $this->get('request_stack')->getCurrentRequest()->request->get('productName');
        $productPrice = $this->get('request_stack')->getCurrentRequest()->request->get('productPrice');

        // Supposons que vous stockiez les produits ajoutés au panier dans une variable de session
        $cartProducts = $this->get('session')->get('cart_products', []);

        // Ajouter le produit au tableau des produits du panier
        $cartProducts[] = [
            'productId' => $productId,
            'productName' => $productName,
            'productPrice' => $productPrice,
            'quantite' => $quantity
        ];

        // Mettre à jour la variable de session avec les nouveaux produits du panier
        $this->get('session')->set('cart_products', $cartProducts);

        // Répondre avec un message de succès ou une redirection vers la page de panier
        return new Response('Product added to cart successfully!');
    }
    
    #[Route('/remove_from_cart', name: 'remove_from_cart_route', methods: ['POST'])]
public function removeFromCart(): Response
{
    // Récupérer l'ID du produit à supprimer envoyé en POST
    $productIdToRemove = $this->get('request_stack')->getCurrentRequest()->request->get('productIdToRemove');

    // Supposons que vous stockiez les produits ajoutés au panier dans une variable de session
    $cartProducts = $this->get('session')->get('cart_products', []);

    // Rechercher et supprimer le produit du panier par son ID
    foreach ($cartProducts as $key => $product) {
        if ($product['productId'] == $productIdToRemove) {
            unset($cartProducts[$key]);
            break; // Arrêter la recherche une fois le produit trouvé et supprimé
        }
    }

    // Mettre à jour la variable de session avec les produits du panier mis à jour
    $this->get('session')->set('cart_products', array_values($cartProducts));

    // Redirection vers la page actuelle après avoir supprimé le produit
    $referer = $this->get('request_stack')->getCurrentRequest()->headers->get('referer');
    return new RedirectResponse($referer);    }

    #[Route('/update_quantity', name: 'update_quantity', methods: ['POST'])]
public function updateQuantity(Request $request): JsonResponse
{
    // Récupérer les données de la requête AJAX
    $productId = $request->request->get('productId');
    $quantity = $request->request->getInt('quantity');

    // Mettre à jour la quantité dans la session ou la base de données
    $cartProducts = $this->get('session')->get('cart_products', []);

    // Rechercher le produit dans le panier et mettre à jour sa quantité
    foreach ($cartProducts as &$product) {
        if ($product['productId'] == $productId) {
            $product['quantity'] = $quantity;
            break;
        }
    }

    // Mettre à jour la variable de session avec les produits du panier mis à jour
    $this->get('session')->set('cart_products', $cartProducts);

    
    // Recalculer le prix total du panier
    $cartTotal = 0;
    foreach ($cartProducts as $product) {
        $cartTotal += $product['productPrice'] * $product['quantity'];
    }

    // Retourner le nouveau prix total au format JSON
    return new JsonResponse(['cartTotal' => $cartTotal]);
}

    
}
