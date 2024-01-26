<?php

namespace App\Command;

use App\Classe\DateTimeFrench;
use App\Classe\MailjetService;
use App\Repository\EventRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Repository\Newsletter\SubscriberRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Envoyer une newsletter qui contient le programme de la semaine à venir',
)]
class SendNewsletterCommand extends Command
{
    private $subscriberRepository;
    private $eventRepository;
    // private $mailer;
    private $mailjet;
    private $router;

    public function __construct(
        SubscriberRepository $subscriberRepository,
        EventRepository $eventRepository,
        // MailerInterface $mailer,
        MailjetService $mailjet,
        UrlGeneratorInterface $router
    ) {
        parent::__construct();
        $this->subscriberRepository = $subscriberRepository;
        $this->eventRepository = $eventRepository;
        // $this->mailer = $mailer;
        $this->mailjet = $mailjet;
        $this->router = $router;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Ecrire dans le terminal
        $io = new SymfonyStyle($input, $output);

        // Récupérer les abonnés valides
        $subscribers = $this->subscriberRepository->findByIsValid(true);

        // Numéro de semaine
        $week = (new \DateTime('now + 1 week'))->format("W");
        $io->info('Préparer la newsletter pour la semaine ' . $week);

        // Titre de la newsletter
        $monday = (new DateTimeFrench('monday this week + 1 week'))->format("j");
        $sunday = (new DateTimeFrench('sunday this week + 1 week'))->format("j F");
        $title = 'Programmation du ' . $monday . ' au ' . $sunday;

        // Récupérer les événements
        $events = $this->eventRepository->findByWeek($week);
        $io->info(count($events) . ' événements la semaine prochaine');

        // On envoie un mail aux utilisateurs
        foreach ($subscribers as $subscriber) {
            // Envoyer un email CLASSIQUE
            // $email = (new TemplatedEmail())
            //     ->from('newsletter@site.fr')
            //     ->to($subscriber->getEmail())
            //     ->subject($title)
            //     ->htmlTemplate('email/program.html.twig')
            //     ->context([
            //         'events' => $events,
            //         'subscriber' => $subscriber,
            //         'title' => $title
            //     ]);

            // $this->mailer->send($email);

            // Envoyer un email MAILJET
            $site = $this->router->generate('home');
            $unsubscribe = $this->router->generate('newsletter_unsubscribe', [
                'id' => $subscriber->getId(),
                'token' => $subscriber->getValidationToken()

            ]);

            $content = "";
            foreach ($events as $event) {
                $date = ($event->getDateAt())->format("Y-m-d");

                $content .= "<ul><li>";
                $content .= "<b>" . $event->getName() . "</b> - ";
                $content .= (new DateTimeFrench($date))->format("l j F - h\h");
                $content .= "</li><p>" . $event->getDescription() . "</p></ul>";
            }

            $this->mailjet->send(
                $subscriber->getEmail(),
                $subscriber->getEmail(),
                $title,
                $variables = compact(
                    'title',
                    'content',
                    'site',
                    'unsubscribe'
                )
            );
        }


        // Retour terminal
        $io->success('Newsletter envoyée');

        return Command::SUCCESS;
    }
}
