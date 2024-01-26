<?php

namespace App\Controller\Admin\Newsletter;

use App\Entity\Newsletter\Subscriber;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SubscriberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscriber::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('isValid');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityLabelInPlural('Abonnés')
            ->setEntityLabelInSingular('Abonné')
            ->setPageTitle('index', 'Abonnés');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable('edit')
            ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'E-mail'),
            DateField::new('createdAt', 'Abonné le')
                ->setDisabled(),
            BooleanField::new('isValid', 'Confirmé')
                ->setDisabled()
        ];
    }
}
