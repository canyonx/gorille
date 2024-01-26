<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\Newsletter\NewsRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Repository\Newsletter\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SendController extends AbstractController
{
    #[Route('/newsletter/send/{id}', name: 'newsletter_send')]
    public function send(
        $id,
        SubscriberRepository $subscriberRepository,
        NewsRepository $newsRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ): Response {
        // Récupère les utilisateurs dont l'email est valide
        /** @var Subscriber */
        $subscribers = $subscriberRepository->findByIsValid(true);

        // Récupère la news
        /** @var News */
        $news = $newsRepository->findOneById($id);

        // On envoie un mail aux utilisateurs
        foreach ($subscribers as $subscriber) {
            $email = (new TemplatedEmail())
                ->from('newsletter@site.fr')
                ->to($subscriber->getEmail())
                ->subject($news->getTitle())
                ->htmlTemplate('email/newsletter.html.twig')
                ->context([
                    'news' => $news,
                    'subscriber' => $subscriber
                ]);

            $mailer->send($email);
        }

        // Newsletter Envoyée, isSent = true
        $news->setIsSent(true);
        $em->persist($news);
        $em->flush();

        $this->addFlash('success', 'Newsletter envoyée');
        return $this->redirectToRoute('admin');
    }
}
