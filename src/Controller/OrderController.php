<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Request $request, Cart $cart): Response
    {
        $user = $this->getUser();

        // ->getValues() permet d'afficher les valeurs d'une propriété de collection
        // Si l'user n'a pas d'adresses, on le rediride vers la page d'ajout d'adresses
        if (!$user->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $user // A voir l'user en cours
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            dd($form->getData());

            // $address->setUser($this->getUser());

            // $this->em->persist($address);
            // $this->em->flush();
            // return $this->redirectToRoute('app_account_address');
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
}
