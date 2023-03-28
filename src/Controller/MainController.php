<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
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
        return $this->render('main/MonProfil.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
