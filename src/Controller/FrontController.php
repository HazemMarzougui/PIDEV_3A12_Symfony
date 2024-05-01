<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit; 

class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();

        return $this->render('base.html.twig', [
            'controller_name' => 'FrontController',
            'produits' => $produits,
        ]);
    }
    
}
