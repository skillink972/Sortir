<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_index')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
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
