<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Utilisateur;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\StripeService;
use Stripe\Charge;
use Stripe\Stripe;


use TCPDF as TCPDF;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;




#[Route('/commande')]
class CommandeController extends AbstractController
{
 #[Route('/new', name: 'app_checkout')]
public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $rep, SessionInterface $session): Response
{
    $commande = new Commande();
    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($request);
    $cartProducts = $this->get('session')->get('cart_products', []);

    if ($form->isSubmitted() && $form->isValid()) {
        $timestamp = time();
        $uniqueInt = $timestamp % (10 ** 9);
        
        $commande->setIdcommande($uniqueInt);
        
        $quantitiesDataJSON = $request->query->get('quantities');
        $quantitiesData = json_decode($quantitiesDataJSON, true);

        foreach ($cartProducts as $index => $item) {
            $panier = new Panier();
            $panier->setIdCommande($uniqueInt);
            $productId = $item['productId'];
            $quantity = isset($quantitiesData[$index]) ? $quantitiesData[$index]['quantity'] : 1;
            $produit = $rep->find($productId);
            
            if ($produit) {
                $panier->setIdProduit($produit);
                $panier->setPrixU($produit->getPrix()); 
                $panier->setQuantite($quantity); 
                $entityManager->persist($panier);
            }
        }

        $entityManager->persist($commande);
        $entityManager->flush();

        // Vider la liste cartProducts
        $session->remove('cart_products');

        // Rediriger en fonction du mode de paiement
        $paymentMethod = $request->request->get('Payments');

        if ($paymentMethod === "Payments") {
            return $this->redirectToRoute('app_show');
        } elseif ($paymentMethod === "Delivery") {
            return $this->redirectToRoute('app_show');
            
        }
        return $this->redirectToRoute('app_show');
    }

    return $this->render('commande/index.html.twig', [
        'form' => $form->createView(),
        'cartProducts' => $cartProducts,
    ]);
}


    #[Route('/show', name: 'app_show')]
    public function index(): Response
    {
        
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
    
        return $this->render('commande/show.html.twig', [
            'controller_name' => 'FrontController',
            'commandes' => $commandes,
        ]);
    }
    
    #[Route('/updateCommande/{id}', name: 'app_Commande_update')]
    public function updateBook($id,CommandeRepository $repo,Request $req,ManagerRegistry $manager){
        $author =$repo->find($id);
        $form = $this->createForm(CommandeType::class,$author);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_show');
        }
        return $this->render('commande/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/Commande/delete/{id}', name: 'app_commande_delete')]
    public function deleteAuthor(ManagerRegistry $manager,CommandeRepository $repo,$id){
        $author = $repo->find($id);
        if ($author){
         

            
        $manager->getManager()->remove($author);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_show');
            }
            else {
                return new Response("commande!");
            }
        } 
        
        #[Route('/dash_commande/search', name: 'app_commande_dash_search', methods: ['GET'])]
public function search(CommandeRepository $commandeRepository, Request $request): JsonResponse
{
    $searchTerm = $request->query->get('search');
    $commandes = $commandeRepository->searchByTerm($searchTerm);
    
    $commandesArray = [];
    foreach ($commandes as $commande) {
        $commandesArray[] = [
            'id' => $commande->getId(),
            'nom' => $commande->getNom(),
            'prenom' => $commande->getPrenom(),
            'adresse' => $commande->getAdresse(),
            'email' => $commande->getEmail(),
            'prixTotale' => $commande->getPrixTotale(),
        ];
    }

    return new JsonResponse(['commandes' => $commandesArray]);
}


        #[Route('/payment', name: 'app_payment1')]
        public function indexp(): Response
        {
            return $this->render('commande/payment.html.twig', [
                'controller_name' => 'PaymentController',
                'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
            ]);
        }


        #[Route('/payment/create-charge', name: 'app_stripe_charge2', methods: ['POST'])]
        public function createCharge(Request $request)
        {
            Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
            Charge::create ([
                    "amount" => 20 * 100,
                    "currency" => "usd",
                    "source" => $request->request->get('stripeToken'),
                    "description" => "Binaryboxtuts Payment Test"
            ]);
            $this->addFlash(
                'success',
                'Payment Successful!'
            );
            return $this->redirectToRoute('app_payment1', [], Response::HTTP_SEE_OTHER);
        } 

    //qrcode 
    #[Route('/QrCode/{id}', name: 'app_QrCode')]
    public function qrGenerator(ManagerRegistry $doctrine, $id, CommandeRepository $rep)
    {
        $em = $doctrine->getManager();
        $commande = $rep->find($id);
      //  $qrcode = QrCode::create($res->getNom() .  " Et le prix est: " . $res->getPrix())
        $qrcode = QrCode::create( " - L'adresse est :". $commande->getAdresse())

            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $writer = new PngWriter();
        $response = new Response($writer->write($qrcode)->getString(),
        Response::HTTP_OK,
        ['content-type' => 'image/png']
    );
    $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qrcode.png');
    $response->headers->set('Content-Disposition', $disposition);
    return $response;
       
 
    }       
     
       

    
    #[Route('/generate-pdf/{id}', name: 'generate_pdf')]
    public function generatePdf($id, PanierRepository $panierRepository, CommandeRepository $commandeRepository): BinaryFileResponse
    {
        // Récupérer la commande avec l'ID
        $commande = $commandeRepository->find($id);

        // Si la commande n'est pas trouvée, renvoyer une erreur
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Récupérer les paniers avec l'ID de la commande
        $paniers = $panierRepository->findBy(['idCommande' => $id]);

        // Instancier TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Paramètres du document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Facture');

        // Ajouter une page
        $pdf->AddPage();
         
        // Titre centré au milieu de la page
         $pdf->SetY($pdf->GetY() + 10); // Déplace le curseur vers le bas
         $pdf->Cell(0, 20, 'Facture', 0, 1, 'C');

        // Contenu du PDF
        $html = '
            
            <p>Date: ' . date('Y-m-d') . '</p>
            <p>Client: ' . $commande->getPrenom() . ' ' . $commande->getNom() . '</p>
            <p>Adresse: ' . $commande->getAdresse() . '</p>
            <p>Téléphone: ' . $commande->getTelephone() . '</p>
            <p>Email: ' . $commande->getEmail() . '</p>
            <br>
            <br>
            <br>
            <table border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <th>ID_Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>';

        foreach ($paniers as $panier) {
            $html .= '
                <tr>
                  
                    <td>' . $panier->getPrixU() . '</td>
                    <td>' . $panier->getQuantite() . '</td>
                    <td>' . ($panier->getPrixU() * $panier->getQuantite()) . '</td>
                </tr>';
        }

        $html .= '</table>';

        // Charger le contenu HTML dans TCPDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Nom du fichier PDF
        $filename = 'facture_commande_' . $id . '.pdf';

        // Enregistrer le PDF temporairement
        $tempFilePath = sys_get_temp_dir() . '/' . $filename;
        $pdf->Output($tempFilePath, 'F');

        // Créer une réponse pour le fichier PDF
        $response = new BinaryFileResponse($tempFilePath);

        // Ajouter les entêtes pour le téléchargement
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        // Supprimer le fichier temporaire après l'envoi
        $response->deleteFileAfterSend(true);

        return $response;
    }
}

