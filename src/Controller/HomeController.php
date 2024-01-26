<?php

namespace App\Controller;

use App\Classe\DateTimeFrench;
use App\Classe\FrenchDateService;
use Symfony\Component\Mime\Email;
use App\Repository\EventRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        EventRepository $eventRepository
    ): Response {

        $weekNumber = (new \DateTime('today'))->format('W');

        return $this->render('home/index.html.twig', [
            'events' => $eventRepository->findByWeek($weekNumber),
            'week' => $weekNumber
            // Si on choisit la navigation js avec tabs
            // 'events' => $eventRepository->findBy([], ['dateAt' => 'ASC'], 28)
        ]);
    }
}
