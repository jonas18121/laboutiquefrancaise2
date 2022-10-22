<?php

namespace App\Controller;

use Stripe\Price;
use Stripe\Stripe;
use Stripe\Product;
use App\Classe\Cart;
use Stripe\Customer;
use App\Entity\Order;
use App\Form\OrderType;
use Stripe\StripeClient;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
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
            

            $order = new Order();
            $order->setUser($user);
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->SetIsPaid(0);

            //quand on passera en production stripe ira chercher les images dans la vrai adresse
            // https:www/homestock.com
            $YOUR_DOMAIN = 'http://127.0.0.1:8000';

            // $storage_for_subscription ira dans line_items qui est dans Session::create
            $storage_for_subscription = [];

            foreach ($cart->getFull() as $key => $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                $this->em->persist($orderDetails);

                // $storage_for_subscription ira dans line_items qui est dans Session::create
                $storage_for_subscription[] = [
                    'price_data' => [ // création du prix
                        'currency' => 'eur',
                        'unit_amount' => $orderDetails->getPrice(),
                        'product_data' => [ // création du produit
                            'name' => $product['product']->getName(),
                            'images' => [$YOUR_DOMAIN  . '/uploads/images/' . $product['product']->getIllustration()]
                        ]
                    ],
                    'quantity' => $orderDetails->getQuantity(),
                ];
            }

            $this->em->persist($order);
            // $this->em->flush();

            // initialisation de stripe version 9.6
            Stripe::setApiKey('sk_test_51Lr0JGEIYZpSxSYQKxrBBaYuc1aEZjhoHKZiM54oUaU8IuxZmYDa5IQJCDaHn3NSjiZ2pKcyQLQK45CIXyLrg5mI00LzAh8spq');

            // création du client
            $stripe = new StripeClient(
                'sk_test_51Lr0JGEIYZpSxSYQKxrBBaYuc1aEZjhoHKZiM54oUaU8IuxZmYDa5IQJCDaHn3NSjiZ2pKcyQLQK45CIXyLrg5mI00LzAh8spq'
            );

            //quand on passera en production stripe ira chercher les images dans la vrai adresse
            // https:www/homestock.com
            $YOUR_DOMAIN = 'http://127.0.0.1:8000';

            // configuration du client
            $customer = Customer::create([
                'name' => $user->getFullname(),
                'email' => $user->getEmail(),
                'phone' => $delivery->getPhone(),
                'description' => 'Cette utilisateur a commandé des produits',
            ]);

            // création du produit
            // $stripe_product = Product::create([
            //     'name' => $product['product']->getName(),
            //     'tax_code' => 'txcd_99999999',
            //     'images' => $YOUR_DOMAIN  . 'uploads/images/' . $product['product']->getIllustration()
            // ]);

            // création du prix
            // $stripe_price =  Price::create([
            //     'unit_amount' => $orderDetails->getPrice(),
            //     'currency' => 'eur',
            //     //'recurring' => ['interval' => 'month'],
            //     'product' => $stripe_product->id,
            //   ]);

            // afficher les infos qu'on veut montrer à l'user
            // création de la session
            $checkout_session = Session::create([
                'client_reference_id' => $customer->id,
                'customer' => $customer->id,
                'line_items' => [[
                    $storage_for_subscription
                ]],
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'success_url' => $YOUR_DOMAIN . '/commande/success/stripeSessionId={CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);
              
            //   header("HTTP/1.1 303 See Other");
            //   header("Location: " . $checkout_session->url);

            dump($checkout_session->url);
            dd($checkout_session);


            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content
            ]);
        }

        return $this->redirectToRoute('app_cart');
    }
}
