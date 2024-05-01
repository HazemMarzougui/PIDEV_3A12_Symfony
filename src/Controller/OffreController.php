<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Dompdf\Dompdf;
use Dompdf\Options;




class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }

    //////////////WORKING////////////////
    #[Route('/{idEvent}', name: 'app_offre_jdida', methods: ['GET'])]
    public function ShowByEvent($idEvent,OffreRepository $offreRepository): Response
    {
        $idEvent = (int)$idEvent;
        $offres = $offreRepository->getOffersByEvent($idEvent);
        return $this->render('offre/index.html.twig', [
            'offres' => $offres
        ]);
    }

    #[Route('/ExportPdf/{idEvent}', name: 'app_pdf', methods: ['GET', 'POST'])]
public function ExportPdf($idEvent, OffreRepository $offreRepository): Response
{
    $idEvent = (int)$idEvent;
    $offres = $offreRepository->getOffersByEvent($idEvent);

    $options = new Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $html = $this->renderView('Offre/pdf.html.twig', [
        'offres' => $offres,
        'idEvent' => $idEvent // Passer l'id de l'événement si nécessaire
    ]);

    $dompdf->loadHtml($html);

    // (Optional) Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait'); // Portrait pour les cartes

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to browser (inline view)
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}

    #[Route('/offre/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Manually validate date fields
            $dateDebut = $offre->getDateDebut();
            $dateFin = $offre->getDateFin();
    
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
    
            // If all validation passes, persist the new Offre
            $entityManager->persist($offre);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_offre_index');
        }
    
        return $this->renderForm('offre/new.html.twig', [
            'offre' => $offre,
            'f' => $form,
        ]);
    }
    
    

    #[Route('/{idOffre}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{idOffre}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offre/edit.html.twig', [
            'offre' => $offre,
            'f' => $form,
        ]);
    }

    #[Route('/{idOffre}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getIdOffre(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
