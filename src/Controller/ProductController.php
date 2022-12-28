<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/produits", name="app_products")
     */
    public function index(Request $request): Response
    {
        $productRepository = $this->em->getRepository(Product::class);

        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $productSearch = $productRepository->findWithSearch($search);
        }
        else {
            $products = $productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $productSearch ?? $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="app_product")
     */
    public function show(string $slug): Response
    {
        $productEntity = $this->em->getRepository(Product::class);
        // $product = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);
        $product = $productEntity->findOneBySlug($slug);

        $products = $productEntity->findByIsBest(1);

        if(!$product){
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
