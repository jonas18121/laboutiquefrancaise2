<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private AdminUrlGenerator $adminUrlGenerator; // permet de gérer une url

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator) 
    {
        $this->entityManager = $entityManager;   
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    /**
     * Permet de rajouter des actions 
     * 
     * exemple
     * ->add('index', 'detail'); rajoute le bouton 'consulter' pour voir le detail du produit
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // On rajoute une action custom
        // ->linkToCrudAction('updatePreperation') permet de lier le bouton d'action à la methode updatePreperation()
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-shipping-fast')->linkToCrudAction('updateDelivery');
        $updateLivery = Action::new('updateLivery', 'Le clois est livré', 'fas fa-truck-loading')->linkToCrudAction('updateLivery');

        return $actions
            // égale aussi ->add('index', 'detail');
            ->add(Crud::PAGE_INDEX, Action::DETAIL) // Dans la page index, on ajoute le bouton action qui permet de voir une commande en détail
            ->add(Crud::PAGE_DETAIL, $updateLivery) // Dans la page détail, on ajoute le custom bouton action qui modifie le status de la commande en 'Le clois a été livré'
            ->add(Crud::PAGE_DETAIL, $updateDelivery) // Dans la page détail, on ajoute le custom bouton action qui modifie le status de la commande en Livraison en cours
            ->add(Crud::PAGE_DETAIL, $updatePreparation) // Dans la page détail, on ajoute le custom bouton action qui modifie le status de la commande en Préparation en cours  
        ;
    }

    /**
     * modifie le status en Préparation en cours
     *
     * @param AdminContext $context - Nous permet d'accéder a l'entité Order
     * @return void
     */
    public function updatePreparation (AdminContext $context) {
        $order = $context->getEntity()->getInstance();

        if ($order->getState() === 2) {
            $action = Action::DETAIL;

            $this->addFlash('warning', 'La commande ' . $order->getReference() . ' est déjà en cours de préparation');
        }else {
            $action = Action::INDEX;

            $order->setState(2);
            $this->entityManager->flush();
            $this->addFlash('success', 'La commande ' . $order->getReference() . ' est en cours de préparation');

            // send mail
            $mail = new Mail();
            $content = 'Bonjour ' . $order->getUser()->getFirstname() . '<br> Votre commande est en cours de préparation';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Bienvenue sur la Boutique Française', $content);
        }

        // Générer une URL
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction($action)
            ->generateUrl()
        ;
        // Redirection
        return $this->redirect($url);
    }

    /**
     * modifie le status en Livraison en cours
     *
     * @param AdminContext $context - Nous permet d'accéder a l'entité Order
     * @return void
     */
    public function updateDelivery (AdminContext $context) {
        $order = $context->getEntity()->getInstance();

        if ($order->getState() === 3) {
            $action = Action::DETAIL;

            $this->addFlash('warning', 'La commande ' . $order->getReference() . ' est déjà en cours de livraison');
        }else {
            $action = Action::INDEX;

            $order->setState(3);
            $this->entityManager->flush();
            $this->addFlash('success', 'La commande ' . $order->getReference() . ' est en cours de livraison');

            // send mail
            $mail = new Mail();
            $content = 'Bonjour ' . $order->getUser()->getFirstname() . '<br> Votre colis est en cours de livraison';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Bienvenue sur la Boutique Française', $content);
        }

        // Générer une URL
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction($action)
            ->generateUrl()
        ;
        // Redirection
        return $this->redirect($url);
    }

    /**
     * modifie le status en Livraison en cours
     *
     * @param AdminContext $context - Nous permet d'accéder a l'entité Order
     * @return void
     */
    public function updateLivery (AdminContext $context) {
        $order = $context->getEntity()->getInstance();

        if ($order->getState() === 4) {
            $action = Action::DETAIL;

            $this->addFlash('warning', 'La commande ' . $order->getReference() . ' a déjà été livré');
        }else {
            $action = Action::INDEX;

            $order->setState(4);
            $this->entityManager->flush();
            $this->addFlash('success', 'La commande ' . $order->getReference() . ' est livré');

            // send mail
            $mail = new Mail();
            $content = 'Bonjour ' . $order->getUser()->getFirstname() . '<br> Votre colis est livré';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Bienvenue sur la Boutique Française', $content);
        }

        // Générer une URL
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction($action)
            ->generateUrl()
        ;
        // Redirection
        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'Passé le'),
            TextField::new('user.fullname', 'Utilisateur'),
            TextField::new('delivery', 'Adresse de livraison')->onlyOnDetail()->renderAsHtml(),// ->renderAsHtml() traduit les balise HTML
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3,
                'Livrée' => 4
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
}
