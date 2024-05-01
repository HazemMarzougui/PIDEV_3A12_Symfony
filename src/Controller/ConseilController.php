<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Entity\Typeconseil;
use App\Entity\Produit;
use App\Form\ConseilType;
use App\Repository\ConseilRepository;
use App\Repository\ProduitRepository;
use App\Repository\TypeConseilRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class ConseilController extends AbstractController
{

    #[Route('/base', name: 'app_base', methods: ['GET'])]
    public function GoToBase(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/conseils',name:'getAll')]
    public function getAll(Request $request,ConseilRepository $repo,PaginatorInterface $paginator){
        $conseil = new Conseil();
        $query = $repo->findAll();
        $conseils = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Current page number, default to 1 if not provided
            3// Number of elements per page
        );
        $conseilsnumber = $repo->conseilsCount(); 
        $conseilCountsByType = $repo->getConseilCountsByType();
        $formConseil = $this->createForm(ConseilType::class, $conseil);
        $formConseil->handleRequest($request);    
        return $this->render('Back/conseil/AfficherConseils.html.twig',[
            'c'=>$conseils,
            'fc' => $formConseil->createView(),
            'number' => $conseilsnumber,
            'conseilCountsByType' => $conseilCountsByType,
        ]);
    }


    #[Route('/sort-by-category-asc', name: 'sort_by_category_asc')]
    public function sortByCategoryAsc(Request $request, ConseilRepository $repo, PaginatorInterface $paginator): Response
    {
        // Fetch sorted conseils
        $conseil = new Conseil();
        $conseils = $repo->findAllSortedByCategoryAsc();

        // Paginate sorted conseils
        $conseilsPaginated = $paginator->paginate(
            $conseils,
            $request->query->getInt('page', 1), // Current page number, default to 1 if not provided
            3 // Number of elements per page
        );
        $conseilsnumber = $repo->conseilsCount(); 
        $conseilCountsByType = $repo->getConseilCountsByType();
        $formConseil = $this->createForm(ConseilType::class, $conseil);
        $formConseil->handleRequest($request);    
        // Render the template with paginated and sorted conseils
        return $this->render('Back/conseil/AfficherConseils.html.twig', [
            'c' => $conseilsPaginated,
            'fc' => $formConseil->createView(),
            'number' => $conseilsnumber,
            'conseilCountsByType' => $conseilCountsByType,
        ]);
    }
/*
    #[Route('/export-to-excel', name: 'export_to_excel')]
    public function exportToExcel(ConseilRepository $repo): Response
    {
        // Fetch the data to export
        $conseils = $repo->findAll();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom Conseil');
        //$sheet->setCellValue('C1', 'Video');
        $sheet->setCellValue('D1', 'Description');
        $sheet->setCellValue('E1', 'Date');
        $sheet->setCellValue('F1', 'Categorie');
        $sheet->setCellValue('G1', 'Produit');

        // Populate data rows
        $row = 2;
        foreach ($conseils as $conseil) {
            $sheet->setCellValue('A' . $row, $conseil->getIdConseil());
            $sheet->setCellValue('B' . $row, $conseil->getNomConseil());
           // $sheet->setCellValue('C' . $row, $conseil->getVideo());
            $sheet->setCellValue('D' . $row, $conseil->getDescription());
            $sheet->setCellValue('E' . $row, $conseil->getDatecreation()->format('Y-m-d H:i:s'));
            $sheet->setCellValue('F' . $row, $conseil->getIdTypec()->getNomtypec());
            $sheet->setCellValue('G' . $row, $conseil->getIdProduit()->getNomProduit());
            $row++;
        }

        // Create a new Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="conseils.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Write the Excel file content to the response
        $writer->save('php://output');
        $response->send();

        return $response;
    }

    #[Route('/search', name: 'conseil_search', methods: ['GET'])]
    public function searchAction(Request $request, ConseilRepository $repo)
    {
        $conseil = new Conseil();
        $query = $request->query->get('query');
        $conseilsnumber = $repo->conseilsCount();
        $conseilCountsByType = $repo->getConseilCountsByType();
        $conseils = $repo->findByNomConseilLike($query);
        $formConseil = $this->createForm(ConseilType::class, $conseil);
        $formConseil->handleRequest($request);    
        return $this->render('Back/conseil/AfficherConseils.html.twig', [
            'c'=>$conseils,
            'fc' => $formConseil->createView(),
            'number' => $conseilsnumber,
            'conseilCountsByType' => $conseilCountsByType,
        ]);
    }*/

    #[Route('/conseilsFront',name:'getAllFront')]
    public function getAllFront(Request $request,ConseilRepository $repo){
        $conseil = new Conseil();
        $conseils = $repo->findAll();
        $conseilsnumber = $repo->conseilsCount(); 
        return $this->render('Front/conseils.html.twig',[
            'c'=>$conseils,
            'number' => $conseilsnumber
        ]);
    }

    #[Route('/conseilsFront/{idc}',name:'getOne')]
    public function getOne(Request $request,ConseilRepository $repo,$idc){
        $conseil = $repo->find($idc);
        return $this->render('Front/conseilsDetails.html.twig',[
            'c'=>$conseil,
        ]);
    }

    #[Route('/addConseil', name: 'conseil_add')]
    public function addConseil(Request $request, ConseilRepository $repo, EntityManagerInterface $entityManager,PaginatorInterface $paginator ,ParameterBagInterface $parameterBag): Response
    {
        $conseil = new Conseil();
        $conseil->setDateCreation(new \DateTime());
        $conseilCountsByType = $repo->getConseilCountsByType();

        $formConseil = $this->createForm(ConseilType::class, $conseil);
        $formConseil->handleRequest($request);   
        $conseilsnumber = $repo->conseilsCount(); 
        $query = $repo->findAll();
        $conseils = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Current page number, default to 1 if not provided
            3 // Number of elements per page
        );
    
        if ($formConseil->isSubmitted() && $formConseil->isValid()) {
            if ($videoFile = $formConseil['video']->getData()) {
                $videoDir = $parameterBag->get('video_dir');    
                $Filename = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move($videoDir , $Filename);

                $conseil->setVideo($Filename);
            }
    
            $entityManager->persist($conseil);
            $entityManager->flush();
    
            return $this->redirectToRoute('getAll');
        }
    
        return $this->render('Back/conseil/AfficherConseils.html.twig', [
            'fc' => $formConseil->createView(),
            'c'=>$conseils,
            'number' => $conseilsnumber,
            'conseilCountsByType' => $conseilCountsByType,
        ]); 
    }

    

    #[Route('/update/{idc}', name: 'conseil_update')]
    public function updateConseil($idc,ConseilRepository $repo,Request $req,EntityManagerInterface $manager,ParameterBagInterface $parameterBag){
        $conseil =$repo->find($idc);
        //$conseil->setDateCreation(new \DateTime());
        $form = $this->createForm(ConseilType::class,$conseil);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            if ($videoFile = $form['video']->getData()) {
                $videoDir = $parameterBag->get('video_dir');    
                $Filename = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move($videoDir , $Filename);

                $conseil->setVideo($Filename);
            }
        $manager->flush();
        return $this->redirectToRoute('getAll');
        }
        return $this->render('Back/conseil/Modifier.html.twig',[
            'fc'=>$form->createView()
        ]);
    }

    #[Route('/conseil/delete/{id}', name: 'conseil_delete')]
    public function deleteConseil(ManagerRegistry $manager,ConseilRepository $repo,$id){
        $conseil = $repo->find($id);
        if ($conseil){     
        $manager->getManager()->remove($conseil);
        $manager->getManager()->flush();
        return $this->redirectToRoute('getAll');
    
        }
         else return new Response("There is no conseil with this ID!");
    }

    

}
