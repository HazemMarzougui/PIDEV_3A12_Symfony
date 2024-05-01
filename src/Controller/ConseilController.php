<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Controller\ExcelController;
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

    #[Route('/conseilsFront', name: 'getAllFront')]
    public function getAllFront(Request $request, ConseilRepository $repo, TypeConseilRepository $repoType, PaginatorInterface $paginator)
    {

        $categoryId = $request->query->getInt('category', -1);
    
        // Fetch artworks based on the selected category filter
        if ($categoryId !== -1) {
            $conseils = $repo->findBy(['idTypec' => $categoryId]);
        } else {
            // If no category is selected (-1), retrieve all artworks
            $conseils = $repo->findAll();
        }

        $conseilsNumber = $repo->conseilsCount();
        $categories = $repoType->findAll();

        $conseilsPaginated = $paginator->paginate(
            $conseils,
            $request->query->getInt('page', 1), // Current page number, default to 1 if not provided
            3 // Number of elements per page
        );
        return $this->render('Front/conseils.html.twig', [
            'c' => $conseilsPaginated,
            'number' => $conseilsNumber,
            'categories' => $categories 
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


    #[Route('/export-to-excel', name: 'export_to_excel')]
    public function generateExcel(ExcelController $excelController): Response
    {
        $filename = $excelController->generateConseilsExcel();
    
        // Generate the full path to the Excel file
        $filePath = $this->getParameter('kernel.project_dir') . '/public/excel/' . $filename;
    
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist.');
        }
        return $this->file($filePath);
    }


    #[Route('/conseilsFront/{idc}',name:'getOne')]
    public function getOne(Request $request,ConseilRepository $repo,TypeConseilRepository $repoType,$idc){
        $conseil = $repo->find($idc);
        $categoryId = $request->query->getInt('category', -1);
    
        // Fetch artworks based on the selected category filter
        if ($categoryId !== -1) {
            $conseils = $repo->findBy(['idTypec' => $categoryId]);
        } else {
            // If no category is selected (-1), retrieve all artworks
            $conseils = $repo->findAll();
        }

        $conseilsNumber = $repo->conseilsCount();
        $categories = $repoType->findAll();

        $conseilsNumber = $repo->conseilsCount();
        return $this->render('Front/conseilsDetails.html.twig',[
            'c'=>$conseil,
            'number' => $conseilsNumber,
            'categories' => $categories 
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
