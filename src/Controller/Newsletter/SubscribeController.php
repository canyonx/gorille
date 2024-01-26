<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\Subscriber;
use App\Form\Newsletter\SubscriberType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscribeController extends AbstractController
{
    /**
     * Formulaire d'inscription à la newsletter
     */
    #[Route('/newsletter/inscription', name: 'newsletter_subscribe')]
    public function subscribe(
        Request $request,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ): Response {

        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du token
            $token = hash('sha256', uniqid());
            $subscriber->setValidationToken($token);

            $em->persist($subscriber);
            $em->flush();

            // Création du mail de confirmation d'inscription
            $email = (new TemplatedEmail())
                ->from('newsletter@site.fr')
                ->to($subscriber->getEmail())
                ->subject('Votre inscription à la newsletter')
                ->htmlTemplate('email/register.html.twig')
                ->context([
                    'subscriber' => $subscriber,
                    'token' => $token
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Inscription à la newsletter en attente de validation');
            return $this->redirectToRoute('home');
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
