<?php

namespace App\Controller\Admin;

use App\Entity\Legal;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LegalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Legal::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['number' => 'ASC'])
            ->setEntityLabelInPlural('Mentions légales')
            ->setEntityLabelInSingular('Mention légale')
            // ->setFilters([DateCalendarFilter::new('datAt')])
            ->setPageTitle('index', 'Mentions légales');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('number', 'Ordre'),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description')
                ->hideOnIndex(),
        ];
    }
}
