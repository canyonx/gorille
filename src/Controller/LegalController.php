<?php

namespace App\Controller;

use App\Repository\LegalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'legal')]
    public function index(
        LegalRepository $legalRepository
    ): Response {

        return $this->render('legal/index.html.twig', [
            'mentions' => $legalRepository->findBy([], ['number' => 'ASC']),
            'title' => 'Mentions légales',
            'subtitle' => 'Informations concenant le site internet'
        ]);
    }
}
