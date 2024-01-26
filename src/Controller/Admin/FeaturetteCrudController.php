<?php

namespace App\Controller\Admin;

use App\Entity\Featurette;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class FeaturetteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Featurette::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['number' => 'ASC'])
            ->setEntityLabelInPlural('A propos')
            ->setEntityLabelInSingular('A propos')
            ->setPageTitle('index', 'A propos');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('number', 'Ordre'),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description')
                ->hideOnIndex(),
            ImageField::new('image', 'Image')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads/')
                ->setUploadedFileNamePattern('[name]-[randomhash].[extension]')
                ->setRequired(false),
        ];
    }
}
