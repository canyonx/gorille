<?php

namespace App\Controller\Admin;

use App\Entity\Setting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Configuration')
            ->setEntityLabelInSingular('Configuration')
            ->setPageTitle('detail', 'Configuration');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new')
            ->disable('index')
            ->disable('delete');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextField::new('subTitle', 'Sous-titre'),
            TextareaField::new('description', 'Description')
                ->hideOnIndex(),
            ImageField::new('image', 'Logo')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads/')
                ->setRequired(false),
            TextField::new('address', 'Adresse'),
            TextField::new('contactTitle', 'Titre contact')
                ->hideOnIndex(),
            TextField::new('phone', 'Téléphone'),
            EmailField::new('email', 'E-mail'),
            UrlField::new('facebook', 'Facebook'),
            UrlField::new('instagram', 'Instagram'),
            UrlField::new('google', 'Google'),
        ];
    }
}
