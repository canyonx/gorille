<?php

namespace App\Controller;

use App\Repository\FeaturetteRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeaturetteController extends AbstractController
{
    #[Route('/qui-sommes-nous', name: 'featurette')]
    public function index(
        FeaturetteRepository $featuretteRepository,
        SettingRepository $settingRepository
    ): Response {

        return $this->render('featurette/index.html.twig', [
            'featurettes' => $featuretteRepository->findBy([], ['number' => 'ASC'], 6),
            'title' => 'Qui sommes-nous',
            'subtitle' => $settingRepository->find(1)->getDescription()
        ]);
    }
}
