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

    $cartProducts = $this->get('session')->get('cart_products', []);

    $cartTotal = 0;
    foreach ($cartProducts as $product) {
        $cartTotal += $product['productPrice']; 
    }

    return $this->render('panier/index.html.twig', [
        'cartProducts' => $cartProducts,
        'cartTotal' => $cartTotal, 

    ]);
}

    
    #[Route('/add_to_cart', name: 'add_to_cart_route', methods: ['POST'])]
    public function addToCart(): Response
    {
        $productId = $this->get('request_stack')->getCurrentRequest()->request->get('productId');
        $quantity = $this->get('request_stack')->getCurrentRequest()->request->get('quantite');
        $productName = $this->get('request_stack')->getCurrentRequest()->request->get('productName');
        $productPrice = $this->get('request_stack')->getCurrentRequest()->request->get('productPrice');

        $cartProducts = $this->get('session')->get('cart_products', []);
        $cartProducts[] = [
            'productId' => $productId,
            'productName' => $productName,
            'productPrice' => $productPrice,
            'quantite' => $quantity
        ];

        $this->get('session')->set('cart_products', $cartProducts);

        return new Response('Product added to cart successfully!');
    }
    
    #[Route('/remove_from_cart', name: 'remove_from_cart_route', methods: ['POST'])]
public function removeFromCart(): Response
{

    $productIdToRemove = $this->get('request_stack')->getCurrentRequest()->request->get('productIdToRemove');

    $cartProducts = $this->get('session')->get('cart_products', []);

    foreach ($cartProducts as $key => $product) {
        if ($product['productId'] == $productIdToRemove) {
            unset($cartProducts[$key]);
            break; 
        }
    }

    $this->get('session')->set('cart_products', array_values($cartProducts));

    $referer = $this->get('request_stack')->getCurrentRequest()->headers->get('referer');
    return new RedirectResponse($referer);    }

    #[Route('/update_quantity', name: 'update_quantity', methods: ['POST'])]
public function updateQuantity(Request $request): JsonResponse
{
 
    $productId = $request->request->get('productId');
    $quantity = $request->request->getInt('quantity');

    $cartProducts = $this->get('session')->get('cart_products', []);

    foreach ($cartProducts as &$product) {
        if ($product['productId'] == $productId) {
            $product['quantity'] = $quantity;
            break;
        }
    }

    $this->get('session')->set('cart_products', $cartProducts);

    $cartTotal = 0;
    foreach ($cartProducts as $product) {
        $cartTotal += $product['productPrice'] * $product['quantity'];
    }

    return new JsonResponse(['cartTotal' => $cartTotal]);
}

    
}