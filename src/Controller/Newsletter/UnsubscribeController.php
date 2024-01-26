<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Se désabonner de la newsletter
 */
class UnsubscribeController extends AbstractController
{
    #[Route('newsletter/resilier/{id}/{token}', name: 'newsletter_unsubscribe')]
    public function unsubscribe(
        Subscriber $subscriber,
        $token,
        EntityManagerInterface $em
    ): Response {

        // Si le token du lien n'est pas le même que celui de la bdd
        if ($subscriber->getValidationToken() != $token) {
            throw $this->createNotFoundException("Non trouvé");
            return $this->redirectToRoute('home');
        }

        $em->remove($subscriber);
        $em->flush();


        $this->addFlash('success', "Vous êtes désabonné de la newsletter");
        return $this->redirectToRoute('home');
    }
}
