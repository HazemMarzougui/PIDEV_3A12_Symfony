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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Psr\Log\LoggerInterface;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

use Endroid\QrCode\Encoding\ErrorCorrectionLevel;



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



    #[Route('/search/evenements', name: 'app_search_evenement_by_name')]
    public function searchEvenementsByName(Request $request, EvenementRepository $evenementRepository): Response
      {
        $searchTerm = $request->query->get('q');
        
        // Perform the search in the repository
        $evenements = $evenementRepository->searchByName($searchTerm);
        
        // Render the search results as HTML (assuming you have a Twig template for rendering)
        return $this->render('evenement/search_results.html.twig', [
            'evenements' => $evenements,
        ]);
    }


     


    #[Route('/evenementlistF', name: 'app_list')]
    public function getAll1(
        EvenementRepository $repo, 
        PaginatorInterface $paginatorInterface,
        Request $request 
       
    ): Response {
        $searchTerm = $request->query->get('q');

        if ($request->isXmlHttpRequest() && $searchTerm) {
            // Perform search query based on the search term
            $data = $repo->search($searchTerm);
        } else {
            
            $data = $repo->findAll();
        }

       
        $evenements = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            5
        );

        

      

        if ($request->isXmlHttpRequest()) {
            
            return new JsonResponse([
                'html' => $this->renderView('evenement/partial/evenement_list.html.twig', ['evenements' => $evenements]),
            ]);
        }

        
        return $this->render('baseF.html.twig', [
            'evenements' => $evenements,
        
           
        ]);
    }
    
    #[Route("/save_location", name: "save_location", methods: ["POST"])]
    public function saveLocation(Request $request, LoggerInterface $logger): Response
    {
        // Récupérer la localisation à partir des données du formulaire
        $userLocation = $request->request->get('userLocation');
        
        // Vous pouvez ensuite traiter $userLocation selon vos besoins, par exemple, le stocker en base de données
        
        // Exemple pour le logger pour le moment
        $logger->info('Location saved: '.$userLocation);

        // Vous pouvez rediriger l'utilisateur vers une autre page ou retourner une réponse JSON
        $response = new JsonResponse([
            'message' => 'Location saved successfully',
            'location' => $userLocation
        ]);
        
        $renderedView = $this->render('evenement/geo.html.twig', [
            'userLocation' => $userLocation
        ]);
        
        // Créer une réponse combinée
        $combinedResponse = new Response();
        $combinedResponse->setContent($renderedView->getContent() . $response->getContent());
        
        // Définir le type de contenu comme HTML
        $combinedResponse->headers->set('Content-Type', 'text/html');
        
        // Retourner la réponse combinée
        return $combinedResponse;
    }     

   
   #[Route('/generate-qr/{id}', name: 'event_generate_qr')]
   public function generateQrCode($id, EvenementRepository $evenementRepository): Response
   {
      
       $evenement = $evenementRepository->find($id);
       
     
       $qrCodeText = sprintf(
        
           "Event ID: %d\nNom: %s\nDate Début: %s\nDate Fin: %s\nDescription: %s",
           $evenement->getIdEvenement(),
           $evenement->getNomEvent(),
           $evenement->getDateDebut()->format('Y-m-d'),
           $evenement->getDateFin()->format('Y-m-d'),
           $evenement->getDescription()
       );
   
      
       $qrCode = Builder::create()
           ->writer(new PngWriter())
           ->data($qrCodeText)
           ->encoding(new Encoding('UTF-8'))
           ->size(200)
           ->margin(10)
           ->build();
   
      
       $response = new Response($qrCode->getString(), Response::HTTP_OK, [
        'Content-Type' => 'image/png',
    ]);
    
   
       return $response;
   }

   
   #[Route('/evenementadd', name: 'app_add_evenement')]
   public function addEvenement(Request $request, ManagerRegistry $manager, ValidatorInterface $validator ,EvenementRepository $evenementRepository ): Response
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
