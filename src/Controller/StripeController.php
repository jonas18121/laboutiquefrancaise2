<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use Stripe\Customer;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="app_stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
        $user = $this->getUser();

        // Quand on passera en production stripe ira chercher les images dans la vrai adresse
        // https://www.example_domain.com
        // Stripe ne peut pas récupéré les images depuis le local 'http://127.0.0.1:8000'
        // Il faut que le site soit en production
        if ('dev' === $_ENV['APP_ENV']) {
            $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        } elseif ('staging' === $_ENV['APP_ENV']){
            $YOUR_DOMAIN = 'https://staging.example_domain.com';
        } else {
            $YOUR_DOMAIN = 'https://www.example_domain.com';
        }

        // $storage_for_subscription ira dans line_items qui est dans Session::create
        $product_for_subscription = [];

        foreach ($cart->getFull() as $key => $product) {

            // $product_for_subscription ira dans line_items qui est dans Session::create
            $product_for_subscription[] = [
                'price_data' => [ // création du prix
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [ // création du produit
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN  . '/uploads/images/' . $product['product']->getIllustration()]
                    ]
                ],
                'quantity' => $product['quantity'],
            ];
        }

        // initialisation de stripe version 9.6
        Stripe::setApiKey('sk_test_51Lr0JGEIYZpSxSYQKxrBBaYuc1aEZjhoHKZiM54oUaU8IuxZmYDa5IQJCDaHn3NSjiZ2pKcyQLQK45CIXyLrg5mI00LzAh8spq');

        // creation du client
        $customer = Customer::create([
            'name' => $user->getFullname(),
            'email' => $user->getEmail(),
            // 'phone' => $delivery->getPhone(),
            'description' => 'Cette utilisateur a commandé des produits',
        ]);

        // afficher les infos qu'on veut montrer à l'user
        // création de la session
        $checkout_session = Session::create([
            'client_reference_id' => $customer->id,
            'customer' => $customer->id,
            'line_items' => [[
                $product_for_subscription
            ]],
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'success_url' => $YOUR_DOMAIN . '/commande/success/stripeSessionId={CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        // redirection vers Stripe
        return $this->redirect($checkout_session->url, 301);
    }
}
