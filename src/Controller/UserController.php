<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/login', name: 'login')]
    public function Openlogin(): Response
    {
        return $this->render('user/Login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/signup', name: 'signup')]
    public function Opensignup(): Response
    {
        return $this->render('user/SignUp.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
