<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin_product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainPicture = $form->get('mainPicture')->getData();
            if ($mainPicture) {
                $fileName = $this->uploadImage($mainPicture, $slugger);
                $product->setMainPicture($fileName);
            }

            $productRepository->add($product);
            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    private function uploadImage(UploadedFile $file, SluggerInterface $slugger): string
    {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );
        } catch (FileException $e) {
            // Handle exception
        }

        return $fileName;
    }
    #[Route('/{id}', name: 'app_admin_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Product $product, ProductRepository $productRepository, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Check if a new file has been uploaded
        $imageFile = $form->get('mainPicture')->getData();
        
        if ($imageFile) {
            // Handle file upload if a new file has been provided
            $fileName = $this->uploadImage($imageFile, $slugger);
            $product->setMainPicture($fileName);
        } else {
            // If no new file has been provided, retain the existing image
            $product->setMainPicture($product->getMainPicture());
        }

        $productRepository->add($product);
        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('admin_product/edit.html.twig', [
        'product' => $product,
        'form' => $form,
    ]);
}



    #[Route('/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product);
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
