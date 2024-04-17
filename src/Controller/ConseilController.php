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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;




class ConseilController extends AbstractController
{

    #[Route('/base', name: 'app_base', methods: ['GET'])]
    public function GoToBase(): Response
    {
        return $this->render('base.html.twig');
    }



        #[Route('/conseils',name:'getAll')]
        public function getAll(Request $request,ConseilRepository $repo){
            $conseil = new Conseil();
            $conseils = $repo->findAll();
            $formConseil = $this->createForm(ConseilType::class, $conseil);
            $formConseil->handleRequest($request);    
            return $this->render('Back/conseil/AfficherConseils.html.twig',[
                'c'=>$conseils,
                'fc' => $formConseil->createView(),
            ]);
        }

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


    #[Route('/addConseil', name: 'conseil_add')]
    public function addConseil(Request $request, ConseilRepository $repo, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag): Response
    {
        $conseil = new Conseil();
        $conseil->setDateCreation(new \DateTime());
    
        $formConseil = $this->createForm(ConseilType::class, $conseil);
        $formConseil->handleRequest($request);    
    
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
            'c' => $repo->findAll(),
        ]); 
    }

/*
    #[Route('/update/{id}', name: 'conseil_update')]
    public function updateConseil($id,ConseilRepository $repo,Request $req,ManagerRegistry $manager){
        $conseil =$repo->find($id);
        $form = $this->createForm(ConseilType::class,$conseil);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $manager->getManager()->flush();
        return $this->redirectToRoute('getAll');
        }
        return $this->render('conseil/AfficherConseils.html.twig',[
            'fc'=>$form->createView()
        ]);
    }*/


    #[Route('/update/{id}', name: 'conseil_update')]
    public function updateConseil($id, ConseilRepository $repo, Request $req, EntityManagerInterface $entityManager): RedirectResponse
    {
        $conseil = $repo->find($id);
        var_dump($conseil); 
    
        if (!$conseil) {
            throw $this->createNotFoundException('Conseil not found');
        }
    
        $form = $this->createForm(ConseilType::class, $conseil);
    
        // Set the data of the form to the entity
        $form->setData($conseil);
    
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            // Redirect to AfficherConseils.html.twig
            return $this->redirectToRoute('getAll');
        }
    
        return $this->render('Back/conseil/AfficherConseils.html.twig', [
            'fc' => $form->createView(),
        ]);
    }
    

    

    /*
     #[Route('/update/{id}', name: 'conseil_update')]
public function updateConseil(Request $request, ConseilRepository $conseilRepository, TypeConseilRepository $typeconseilRepository,ProduitRepository $produitRepository, EntityManagerInterface $entityManager,int $id): JsonResponse {
    $conseil = $conseilRepository->find($id);
    
    if (!$conseil) {
        return new JsonResponse(['error' => 'Conseil not found'], 404);
    }

    // Get the ID of Typeconseil and Produit
    $typecId = $conseil->getIdTypec()->getIdtypec();
    $produitId = $conseil->getIdProduit()->getIdProduit();

    // Modify the Conseil entity properties here
    // For example, if you want to update the nomConseil property:
    $conseil->setNomConseil('Updated Nom'); // Change 'Updated Nom' to the new value
    
    // Persist changes to the database
    $entityManager->flush();

    // Prepare data to return
    $data = [
        'nomConseil' => $conseil->getNomConseil(),
        'video' => $conseil->getVideo(),
        'description' => $conseil->getDescription(),
        'idTypec' => $typecId,
        'idProduit' => $produitId,
    ];

    return new JsonResponse($data);*/

    

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
