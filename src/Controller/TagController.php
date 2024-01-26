<?php

namespace App\Controller;

use App\Repository\TagRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TagController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'tag')]
    public function index(
        string $slug,
        Request $request,
        TagRepository $tagRepository,
        EventRepository $eventRepository
    ): Response {

        if ($slug === 'tout') {
            $categorie = 'tout';
        } else {
            // Récupère le tag de la page en cours
            $categorie = $tagRepository->findOneBy(['slug' => $slug]);
        }

        // Si le tag $categorie n'existe pas
        if (!$categorie) {
            return $this->redirectToRoute('home');
        }

        // Récupère les événements pour la pagination
        $page = $request->query->getInt('page', 1);
        $offset = max(0, ($page - 1) * EventRepository::PAGINATOR_PER_PAGE);
        $paginator = $eventRepository->getEventPaginatorByTag($categorie, $offset);

        // Nombre de pages 
        $pages = ceil(count($paginator) / EventRepository::PAGINATOR_PER_PAGE);

        // Si la page demandée est supérieure au nombre de pages
        if ($page > $pages) {
            return $this->redirectToRoute('tag', [
                'slug' => $slug
            ]);
        }

        return $this->render('tag/index.html.twig', [
            'categorie' => $categorie,
            'slug' => $slug,
            // 'events' => $eventRepository->findByEventWithTag($categorie),
            // pagination 
            'events' => $paginator,
            'previous' => $page - 1,
            'next' => $page + 1,
            'pages' => $pages,
        ]);
    }
}
