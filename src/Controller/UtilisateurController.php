<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UtilisateurController extends AbstractController
{
    #[Route('/base', name: 'app_base', methods: ['GET'])]
    public function GoToBase(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/users',name:'getAll')]
    public function getAll(Request $request,UtilisateurRepository $repo){
        $user = new Utilisateur();
        $users = $repo->findAll();    
        return $this->render('utilisateur/AfficherUtilisateur.html.twig',[
            'c'=>$users,
        ]);
    }
    
    #[Route('user/adduser', name: 'user_add')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRole("Admin");

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('getAll');
        }

        return $this->render('utilisateur/Ajouter.html.twig', [
            'fc' => $form->createView(),
        ]);
    }

    

    #[Route('user/update/{id}', name: 'user_update')]
    public function updateuser($id, UtilisateurRepository $repo, Request $request, EntityManagerInterface $entityManager): Response{
    $user = $repo->find($id);
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }
    
    $form = $this->createForm(UtilisateurType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // The form was submitted and is valid, so update the user entity
        $entityManager->flush();

        // Redirect or return a response
        return $this->redirectToRoute('getAll'); // Change 'your_redirect_route' to your actual route name
    }

    // Render the form template with the form
    return $this->render('utilisateur/update.html.twig', [
        'fc' => $form->createView(),
    ]);

    }
    
    
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function deleteuser(ManagerRegistry $manager,UtilisateurRepository $repo,$id){
        $user = $repo->find($id);
        if ($user){     
        $manager->getManager()->remove($user);
        $manager->getManager()->flush();
        return $this->redirectToRoute('getAll');
    
        }
         else return new Response("There is no user with this ID!");
    }/*
#[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
{
    $user = new Utilisateur();
    //$user->setDateCreation(new \DateTime());

    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setRole("Client");
        // Encode the password before setting it
        $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        // Persist and flush the user
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    // Render the form
    return $this->render('login.html.twig', [
        'form' => $form->createView(),
    ]);
}*/
}
