<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Cart $cart): Response
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

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="app_order_reacp", methods={"POST"})
     */
    public function add(Request $request, Cart $cart): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(OrderType::class, null, [
            'user' => $user // A voir l'user en cours
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $date = new \DateTime();

            // $carriers = $form['carriers']->getData();
            // $carriers = $form->getData()['carriers'];
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $delivery_content .= '<br/>' . $delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .= '<br/>' . $delivery->getCompany();
            }

            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $delivery_content .= '<br/>' . $delivery->getCountry();
            

            $reference = 'REF-' . $date->format('Y-m-d') . '_' . uniqid();

            $order = new Order();
            $order->setReference($reference);
            $order->setUser($user);
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->SetState(0);

            foreach ($cart->getFull() as $key => $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $orderDetails->setImage($product['product']->getIllustration());

                $this->em->persist($orderDetails);
            }

            $this->em->persist($order);
            $this->em->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('app_cart');
    }
}