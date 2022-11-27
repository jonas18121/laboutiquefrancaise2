<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/compte/adresses", name="app_account_address")
     */
    public function index(): Response
    {

        return $this->render('account/address.html.twig', [
            
        ]);
    }

     /**
     * @Route("/compte/ajouter-une-adresse", name="app_account_address_add")
     */
    public function add(Request $request, Cart $cart): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            // s'il y a des produits dans le panier, on redirige vers la page commande
            // sinon on redirige vers la page adresses
            if ($cart->get()) {
                return $this->redirectToRoute('app_order');
            }
            else {
                return $this->redirectToRoute('app_account_address');
            }
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/compte/modifier-une-adresse/{id}", name="app_account_address_edit")
     */
    public function edit(int $id, Request $request): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getuser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($address);
            $this->em->flush();
            return $this->redirectToRoute('app_account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="app_account_address_delete")
     */
    public function delete(int $id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if ($address || $address->getuser() == $this->getUser()) {
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_account_address');

    }
}
