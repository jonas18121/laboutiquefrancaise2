<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre du header'),
            TextEditorField::new('content', 'Contenue du header'),
            TextField::new('btnTitle', 'Titre du bouton'),
            TextField::new('btnUrl', 'Url du bouton'),
            ImageField::new('illustration')
                ->setBasePath('uploads/images/header')
                ->setUploadDir('public/uploads/images/header')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(true),
                // ->onlyOnIndex() Afficher l'image seulement dans l'index
                // ->onlyOnForms() Afficher l'image seulement dans le formulaire
        ];
    }
    
}
