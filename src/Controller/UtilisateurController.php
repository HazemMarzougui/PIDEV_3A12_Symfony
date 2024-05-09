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
use App\Service\FileUploader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Controller\TwilioSmsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Form\SearchFormType;

class UtilisateurController extends AbstractController
{
    #[Route('/base', name: 'app_base', methods: ['GET'])]
    public function GoToBase(): Response
    {
        return $this->render('base.html.twig');
    }
    #[Route('/front', name: 'app_front', methods: ['GET'])]
    public function GoToFront(): Response
    {
        return $this->render('front\index.html.twig');
    }

    #[Route('/users',name:'getAll')]
    public function getAll(Request $request,UtilisateurRepository $repo){
        // Get user count per role
        $adminCount = $repo->countByRole('Admin');
        $clientCount = $repo->countByRole('Client');
 // Create and handle the search form
 $searchForm = $this->createForm(SearchFormType::class);
 $searchForm->handleRequest($request);

 // Get search query
 $searchQuery = $searchForm->get('searchQuery')->getData();

 // Get user count per role
 $adminCount = $repo->countByRole('Admin');
 $clientCount = $repo->countByRole('Client');

 // Perform search if the form is submitted and valid
 if ($searchForm->isSubmitted() && $searchForm->isValid()) {
     // Perform search query using $searchQuery
     $users = $repo->findBySearchQuery($searchQuery); // Implement this method in your repository
 } else {
     // If no search query, retrieve all users
     $users = $repo->findAll();
 }

 return $this->render('utilisateur/AfficherUtilisateur.html.twig', [
     'c' => $users,
     'adminCount' => $adminCount,
     'clientCount' => $clientCount,
     'searchForm' => $searchForm->createView(),
 ]);
    }
    
    #[Route('user/adduser', name: 'user_add')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsActif(true);
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

    
    #[Route('/user/update/{id}', name: 'user_update')]
    public function updateuser($id, UtilisateurRepository $repo, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ParameterBagInterface $parameterBag): Response{
        $user = $repo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Create the form and handle the request
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle photo file upload
            $photoFile = $user->getPhotoFile();
            if ($photoFile = $form['photo']->getData()) {
                $photoDir = $parameterBag->get('image_directory');    
                $Filename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($photoDir , $Filename);

                $user->setPhoto($Filename);
            }
            // Update the user entity with the hashed password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
    
            // Persist changes to the database
            $entityManager->flush();
    
            // Redirect to a route after successful update
            return $this->redirectToRoute('getAll'); // Change 'getAll' to your actual route name
        }
    
        // Render the form template with the form and user data
        return $this->render('utilisateur/update.html.twig', [
            'fc' => $form->createView(),
            'user' => $user,
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
#[Route('/forgot', name: 'app_forgot', methods: ['GET', 'POST'])]
public function forgotPassword(Request $request, TwilioSmsService $twilioSmsService): Response
{
    if ($request->isMethod('POST')) {
        // Assuming you have a form with an email input
        $number = $request->request->get('number');
        $email = $request->request->get('email');

        // Generate a random number (e.g., 6-digit)
        $randomNumber = mt_rand(100000, 999999);

        // Store the random number somewhere temporarily (e.g., session)
        $request->getSession()->set('reset_random_number', $randomNumber);
        $request->getSession()->set('reset_email', $email);

// Send the SMS
        $twilioSmsService->sendSms($randomNumber,$number);
        // Redirect the user to a page where they can input the code
        return $this->redirectToRoute('verify_code');
    }

    return $this->render('utilisateur/Forgotpass1.html.twig');
}

#[Route('/verify-code', name: 'verify_code', methods: ['GET', 'POST'])]
public function verifyCode(Request $request): Response
{
    // Retrieve the random number stored in the session
    $randomNumber = $request->getSession()->get('reset_random_number');
    $email = $request->getSession()->get('reset_email');


    // Assuming you have a form with a code input field
    $submittedCode = $request->request->get('code');

    // Check if the form is submitted and the code is correct
    if ($request->isMethod('POST') && $submittedCode == $randomNumber) {
        // Code verification successful
        // Redirect the user to a page where they can reset their password
        return $this->redirectToRoute('change_pwd', ['email' => $email]);
    }

    // If code verification failed or the form is not submitted, render the verification code form
    return $this->render('utilisateur/verify_code.html.twig', [
        'error' => 'Invalid code. Please try again.', // You can customize this error message
    ]);
}

#[Route('/change-pwd', name: 'change_pwd', methods: ['GET', 'POST'])]
public function changepwd(Request $request, EntityManagerInterface $entityManager,  UserPasswordHasherInterface $userPasswordHasher): Response
{

    // Retrieve the email from the query parameters
    $email = $request->getSession()->get('reset_email');

   // $email = $request->query->get('email');
 // Retrieve the user entity by email
    $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

    // If the user is not found, handle the error (e.g., show an error message)
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    // Handle changing the password (assuming you have a form with new password input)
    if ($request->isMethod('POST')) {
       $plainPassword = $request->request->get('plainPassword');
        // Encode the new password
        $encodedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);

        // Update the user's password
        $user->setPassword($encodedPassword);

        // Persist the changes to the database
        $entityManager->flush();

        // Redirect the user to a confirmation page
        return $this->redirectToRoute('app_login');
    }

    return $this->render('utilisateur/changepwd.html.twig', ['email' => $email]);
}
#[Route('/statistics', name: 'user_statistics')]
public function userStatistics(UtilisateurRepository $userRepository): Response
{
    // Get user count per role
    $adminCount = $userRepository->countByRole('Admin');
    $clientCount = $userRepository->countByRole('Client');

    // Pass the data to the Twig template
    return $this->render('AfficherUtilisateur.html.twig', [
        'adminCount' => $adminCount,
        'clientCount' => $clientCount,
    ]);
}

}
