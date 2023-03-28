<?php

namespace App\Controller;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name:'sortie')]
class SortieController extends AbstractController
{

    #[Route('/afficher', name: '_afficher')]
    public function afficher(
        SortieRepository $sortieRepository
    ):Response
    {
        $sortiesDontJeSuisOrganisateur = $sortieRepository->findBy(
            [
                "organisateur" => $this->getUser()->getUserIdentifier()
            ]
        );
        $sortiesAuxquellesJeSuisInscrit = $sortieRepository->findBy(
            [
                "participants" => $this->getUser()->getUserIdentifier()
            ]
        );
        $sortiesAuxquellesJeNeSuisPasInscrit = $sortieRepository->findBy(
            [

            ]
        );

        return $this->render('sortie/afficher.html.twig');
    }

    #[Route('/creer', name: '_creer')]
    public function creer(): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        return $this->render('sortie/creer.html.twig',
            compact('sortieForm')
        );
    }

}
