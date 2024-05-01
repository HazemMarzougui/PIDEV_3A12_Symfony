<?php

namespace App\Controller;
use App\Service\TwilioSMSService;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Twilio\Rest\Client;


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


    #[Route('/allevents/export', name: 'export_events_excel')]
     public function exportEventsExcel(EvenementRepository $repo): Response
    {
        // Retrieve reservations from the database
        $events = $repo->findAll();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = ['Nom', 'Date Debut', 'Date Fin', 'Description'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
        }

        // Add reservation data
        foreach ($events as $rowIndex => $event) {
            $sheet->setCellValue('A' . ($rowIndex + 2), $event->getNomEvent());
            $sheet->setCellValue('B' . ($rowIndex + 2), $event->getDateDebut());
            $sheet->setCellValue('C' . ($rowIndex + 2), $event->getDateFin());
            $sheet->setCellValue('D' . ($rowIndex + 2), $event->getDescription());
        }

        // Save Excel file
        $excelFilename = 'events_export.xlsx';
        $excelFilePath = $this->getParameter('kernel.project_dir') . '/public/' . $excelFilename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($excelFilePath);

        // Return the Excel file as a BinaryFileResponse
        return new BinaryFileResponse($excelFilePath);
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
   public function addEvenement(Request $request, ManagerRegistry $manager, ValidatorInterface $validator ): Response
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

         // $sid = $this->getParameter('');
        //  $token = $this->getParameter('');
          $twilioClient = new TwilioSMSService('','');

           // Send SMS using Twilio
           try {
            $twilioSMSService->sendSMS(
                '+21653658515', // to
                "New event 
                   added: " . $newEvenement->getNomEvent() . " on " . $newEvenement->getDateDebut()->format('Y-m-d')
            );

            // Handle success or log the message SID
        } catch (\Exception $e) {
               // Handle Twilio API exception
           }

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
