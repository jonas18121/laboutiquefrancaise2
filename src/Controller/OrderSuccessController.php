<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    
    /**
     * @Route("/commande/success/{stripeSessionId}", name="app_order_validate")
     */
    public function index(string $stripeSessionId, Cart $cart): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            $this->addFlash('alert', 'Une Erreur c\'est produite');
            return $this->redirectToRoute('app_home');
        }

        if (!$order->isPaid()) {
            $cart->remove();
            $order->setIsPaid(1);
            $this->em->flush();

            $mail = new Mail();
            $content = 'Bonjour ' . $order->getUser()->getFirstname() . '<br> Merci pour votre commande vous êtes bien hein....';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'c\'est validé pour vous', $content);
            $this->addFlash('success', 'Votre commande s\'est correctement déroulée');
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
