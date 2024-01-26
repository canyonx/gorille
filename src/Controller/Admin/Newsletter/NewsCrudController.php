<?php

namespace App\Controller\Admin\Newsletter;

use App\Entity\Newsletter\News;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
            ->add('createdAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityLabelInPlural('Newsletters')
            ->setEntityLabelInSingular('Newsletter')
            ->setPageTitle('index', 'Newsletters');
    }

    public function configureActions(Actions $actions): Actions
    {
        // Boutton perso permet d'envoyer la newsletter
        $sendnews = Action::new('sendnews', 'Envoyer la newsletter', 'fas fa-paper-plane')
            ->linkToRoute(
                'newsletter_send',
                fn ($entity) => ['id' => $entity->getId()]
            )
            ->displayIf(fn ($entity) => !$entity->getIsSent());

        return $actions
            ->add('index', 'detail')
            ->add('detail', $sendnews)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->displayIf(fn ($entity) => !$entity->getIsSent());
            })
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $action->displayIf(fn ($entity) => !$entity->getIsSent());
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('createdAt', 'Crée le')
                ->setDisabled()
                ->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('content', 'Contenu')
                ->onlyOnForms()
                ->setNumOfRows(30),
            TextareaField::new('content', 'Contenu')
                ->onlyOnDetail()
                ->renderAsHtml(),
            BooleanField::new('isSent', 'Envoyée')
                ->setDisabled(),
        ];
    }
}
