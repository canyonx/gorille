<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmController extends AbstractController
{
    /**
     * Validation de l'email utilisateur
     */
    #[Route('/newsletter/confirmation/{id}/{token}', name: 'newsletter_confirm')]
    public function confirm(
        Subscriber $subscriber,
        $token,
        EntityManagerInterface $em
    ): Response {
        // Si le token du lien n'est pas le même que celui de la bdd
        if ($subscriber->getValidationToken() != $token) {
            throw $this->createNotFoundException("Non trouvé");
            return $this->redirectToRoute('home');
        }

        // On valide l'abonné
        $subscriber->setIsValid(true);

        $em->persist($subscriber);
        $em->flush();

        $this->addFlash('success', "Votre inscription à la newsletter est confirmée");
        return $this->redirectToRoute('home');
    }
}
