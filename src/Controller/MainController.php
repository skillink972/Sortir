<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_index')]
    public function index(
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        $user = $participantRepository->findOneBy(
            [
                'id'        => $this->getUser()
            ]
        );
        $sorties = $sortieRepository->findBy(
            [
                'etat'      => 2,
                'campus'    => $user->getCampus()
            ]
        );
        return $this->render('main/index.html.twig',
            compact( 'sorties')
        );
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/MonProfil', name: 'app_MonProfil')]
    public function MonProfil(): Response
    {
        return $this->render('main/index.html.twig');
    }


    #[Route('/participant', name: 'main_participant')]
    public function participant(): Response
    {
        return $this->render('participant/ModifierMonProfil.html.twig');
    }

}
