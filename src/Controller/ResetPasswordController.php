<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;    
    }
    
    /**
     * @Route("/mot-de-passe-oublie", name="app_reset_password")
     */
    public function index(Request $request): Response
    {
        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            
            
            if ($user) {
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();
                

                // Pour envoyer un mail avec un lien mais mailjet a supprimer mon compte
                // $url = $this->generateUrl('app_update_password', [
                //     'token' => $reset_password->getToken()
                // ]) ;

                // $mail = new Mail();
                // $content = 'Bonjour ' . $user->getFirstname() . '<br> Vous voulez, réinitisaliser votre mot de passe.<br><br>';
                // $content = "Clique sur ce lien pour <a href='" . $url ."'>mettre à jour votre mot de passe</a>.";

                // $mail->send($user->getEmail(), $user->getFirstname(), 'Réinitisaliser mot de passe sur la Boutique Française', $content);
                // $this->addFlash('success', 'Votre demande est envoyer, vous recevrez un email pour mettre à jour votre mot de passe.');

                return $this->redirectToRoute('app_update_password', [
                    'token' => $reset_password->getToken()
                ]);
            } else {
                $this->addFlash('error', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="app_update_password")
     */
    public function update(string $token, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('app_reset_password');
        }

        // Le temps pour réinitialiser le password est de 3 heures a partir de la création de la date
        $date_for_reset_valid = $reset_password->getCreatedAt()->modify('+3 hour');

        // Date actuelle
        $now = new \DateTimeImmutable();

        if ($now > $date_for_reset_valid) {
            $this->addFlash('warning', 'Votre mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('app_reset_password');
        }

        $user = $reset_password->getUser();

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $new_pwd);

            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}