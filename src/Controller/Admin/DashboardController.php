<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Legal;
use App\Entity\Partner;
use App\Entity\Setting;
use App\Entity\Featurette;
use App\Entity\Newsletter\News;
use App\Repository\TagRepository;
use App\Repository\EventRepository;
use App\Entity\Newsletter\Subscriber;
use App\Repository\PartnerRepository;
use App\Controller\Admin\EventCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Newsletter\SubscriberRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EventRepository $eventRepository,
        private SubscriberRepository $subscriberRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(EventCrudController::class)->generateUrl());

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Le Gorille');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Événements');

        // Accueil dashboard
        // yield MenuItem::linkToDashboard('Événements', 'fa fa-home', Event::class);

        // Tous les événements
        // yield MenuItem::linkToCrud('Évenement', 'fas fa-list', Event::class);

        // Événements passés
        yield MenuItem::linkToCrud('Passés', 'fa fa-chevron-left', Event::class)
            ->setQueryParameter('filters[dateAt][comparison]', '<')
            ->setQueryParameter('filters[dateAt][value]', (new \DateTime('today'))->format('Y-m-d\TH:i'))
            ->setBadge(count($this->eventRepository->findByPeriod("<")));

        // Événements a venir
        yield MenuItem::linkToCrud('A venir', 'fa fa-chevron-right', Event::class)
            ->setQueryParameter('filters[dateAt][comparison]', '>')
            ->setQueryParameter('filters[dateAt][value]', (new \DateTime('today'))->format('Y-m-d\TH:i'))
            ->setBadge(count($this->eventRepository->findByPeriod(">=")));

        // Événements du mois en cours
        // yield MenuItem::linkToCrud((new \DateTime('today'))->format('F'), 'fa fa-paperclip', Event::class)
        //     ->setQueryParameter('filters[dateAt][comparison]', 'between')
        //     ->setQueryParameter('filters[dateAt][value1]', (new \DateTime('first day of this month'))->format('Y-m-d\TH:i'))
        //     ->setQueryParameter('filters[dateAt][value2]', (new \DateTime('last day of this month'))->format('Y-m-d\TH:i'));

        // Tags
        yield MenuItem::linkToCrud('Tags', 'fas fa-tag', Tag::class);


        // Newsletter
        yield MenuItem::section('Newsletter');
        // Abonnés
        yield MenuItem::linkToCrud('Abonnés', 'fas fa-users', Subscriber::class)
            ->setBadge(count($this->subscriberRepository->findAll()));
        // Lettres
        yield MenuItem::linkToCrud('Newsletters', 'fas fa-envelope', News::class);


        // Administrateur uniquement
        yield MenuItem::section('Administration')
            ->setPermission('ROLE_ADMIN');
        // Apropos
        yield MenuItem::linkToCrud('A propos', 'fas fa-star', Featurette::class)
            ->setPermission('ROLE_ADMIN');
        // Partenaires
        yield MenuItem::linkToCrud('Partenaires', 'fas fa-handshake', Partner::class)
            ->setPermission('ROLE_ADMIN');
        // Mentions légales
        yield MenuItem::linkToCrud('Mentions légales', 'fas fa-list', Legal::class)
            ->setPermission('ROLE_ADMIN');
        // Configuration
        yield MenuItem::linkToCrud('Configuration', 'fas fa-cog', Setting::class)
            ->setQueryParameter('crudAction', 'detail')
            ->setQueryParameter('entityId', '1')
            ->setPermission('ROLE_ADMIN');
        // Utilisateurs
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)
            ->setPermission('ROLE_ADMIN');
    }
}
