<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class EventCrudController extends AbstractCrudController
{
    protected $slugger;
    protected $cacheManager;

    public function __construct(SluggerInterface $slugger, CacheManager $cacheManager)
    {
        $this->slugger = $slugger;
        $this->cacheManager = $cacheManager;
    }

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('dateAt')
            ->add('name')
            ->add('tags');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['dateAt' => 'ASC'])
            ->setEntityLabelInPlural('Événements')
            ->setEntityLabelInSingular('Événement')
            // ->setFilters([DateCalendarFilter::new('datAt')])
            ->setPageTitle('index', 'Événements');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('dateAt', 'Date')
                ->setFormat("EEE d MMMM - HH'h'mm"),
            TextField::new('name', 'Nom'),
            ArrayField::new('Tags', 'Tags')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            AssociationField::new('tags', 'Tags')
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex(),
            TextEditorField::new('description')
                ->hideOnIndex(),
            ImageField::new('image', 'Image')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads/')
                ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
                ->setRequired(false),
            TextareaField::new('soundcloud', 'SoundCloud')
                ->hideOnIndex(),
            TextField::new('youtube', 'YouTube')
                ->hideOnIndex(),

        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $event = $entityInstance;

        $event->setSlug(
            $this->slugger->slug(
                $event->getName() . '-' . $event->getDateAt()->format('Y-m-d')
            )->lower()
        );

        $entityManager->persist($event);
        $entityManager->flush();
    }
}
