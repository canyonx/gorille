<?php

namespace App\Controller;

use App\Classe\TagService;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeekController extends AbstractController
{
    #[Route('/semaine/{week}', name: 'week')]
    public function week(
        int $week,
        EventRepository $eventRepository
    ): Response {

        if ($week > 52 || $week < 1) {
            return $this->redirectToRoute('home');
        }

        $events = $eventRepository->findByWeek($week);

        return $this->render('week/index.html.twig', [
            'events' => $events,
            'week' => $week
        ]);
    }
}
