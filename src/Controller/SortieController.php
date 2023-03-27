<?php

namespace App\Controller;
use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/CreeSortie', name: 'CreeSortie')]
    public function nouveau(): Response
    {
        $Sortie = new Sortie();
        $SortieForm = $this->createForm(SortieType::class, $Sortie);

        return $this->render('cree_sortie/index.html.twig',
            compact('SortieForm')
        );
    }
}
