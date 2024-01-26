<?php

namespace App\Controller;

use App\Classe\TagService;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/evenement/{slug}', name: 'event')]
    public function index(
        string $slug,
        EventRepository $eventRepository
    ): Response {

        $event = $eventRepository->findOneBySlug($slug);

        if (!$event) {
            return $this->redirectToRoute('home');
        }

        return $this->render('event/index.html.twig', [
            'event' => $event,
            'week' => $event->getDateAt()->format("W")
        ]);
    }
}
