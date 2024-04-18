<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }
    
    // List all Evenements
    #[Route('/evenementlist', name: 'app_list_evenements')]
    public function getAll(EvenementRepository $repo): Response
    {
        $evenements = $repo->findAll();
        return $this->render('evenement/display.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenementlistF', name: 'app_list')]
    public function getAll1(EvenementRepository $repo): Response
    {
        $evenements = $repo->findAll();
        return $this->render('baseF.html.twig', [
            'evenements' => $evenements,
        ]);
    }

   // Add an Evenement
#[Route('/evenementadd', name: 'app_add_evenement')]
public function addEvenement(Request $request, ManagerRegistry $manager, ValidatorInterface $validator): Response
{
    $newEvenement = new Evenement();
    $form = $this->createForm(EvenementType::class, $newEvenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Manually validate date fields
        $dateDebut = $newEvenement->getDateDebut();
        $dateFin = $newEvenement->getDateFin();

        // Validate dateDebut
        if ($dateDebut !== null) {
            $errors = $validator->validate($dateDebut, [
                new Assert\DateTime(['message' => 'La date de début doit être une date valide.'])
            ]);

            if (count($errors) > 0) {
                // Handle validation errors
            }
        }

        // Validate dateFin
        if ($dateFin !== null) {
            $errors = $validator->validate($dateFin, [
                new Assert\DateTime(['message' => 'La date de fin doit être une date valide.']),
                new Assert\GreaterThanOrEqual([
                    'value' => $dateDebut,
                    'message' => 'La date de fin doit être supérieure ou égale à la date de début.'
                ])
            ]);

            if (count($errors) > 0) {
                // Handle validation errors
            }
        }

        // If all validation passes, persist the new Evenement
        $manager->getManager()->persist($newEvenement);
        $manager->getManager()->flush();

        return $this->redirectToRoute('app_list_evenements');
    }

    return $this->render('evenement/add.html.twig', [
        'f' => $form->createView(),
    ]);
}

// #[Route('/{idEvenement}', name: 'app_delete_evenement', methods: ['POST'])]
//     public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
//     {
//         if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
//             $entityManager->remove($evenement);
//             $entityManager->flush();
//         }

//         return $this->redirectToRoute('app_list_evenements', [], Response::HTTP_SEE_OTHER);
//     }

    
     //Delete an Evenement
     #[Route('/evenementdelete/{id}', name: 'app_delete_evenement')]
    public function deleteEvenement($id, ManagerRegistry $manager, EvenementRepository $repo): Response
    {
        $evenement = $repo->find($id);
        $manager->getManager()->remove($evenement);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_list_evenements');
     }
    
    // Update an Evenement
    #[Route('/evenementupdate{idEvenement}', name: 'app_update_evenement')]
    public function updateEvenement($idEvenement, Request $request, ManagerRegistry $manager, EvenementRepository $repo): Response
    {
        $evenement = $repo->find($idEvenement);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->getManager()->persist($evenement);
            $manager->getManager()->flush();
            return $this->redirectToRoute('app_list_evenements');
        }
        return $this->render('evenement/add.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    

    
    #[Route('/evenement/{id}', name: 'app_evenement_show')]
    public function show(Evenement $evenement): Response
    {
        // Fetch specific offers related to the event
        $offers = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->findOffersByEventId($evenement->getId());
    
        return $this->render('event/show.html.twig', [
            'evenement' => $evenement, // Corrected variable name
            'offres' => $offers, // Assuming 'offres' is the variable name expected in the template
        ]);
    }
    
}
