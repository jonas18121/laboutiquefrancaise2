<?php

namespace App\Controller;

use App\Classe\Mail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {

        $mail = new Mail();
        $mail->send('jonathanDevelopper971@gmail.com', 'Jonas', 'test', 'ceci est un test');

        return $this->render('home/index.html.twig');
    }
}
