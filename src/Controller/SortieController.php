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

    #[Route('/Afficher', name: '_afficher')]
    public function afficher(
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository
    ):Response {
        $sortiesDontJeSuisOrganisateur = $sortieRepository->findBy(
            [
            "organisateur" => $this->getUser()->getUserIdentifier()
            ]
        );
        $sortiesAuxquellesJeSuisInscrit = $sortieRepository->findBy(
            [
                "participant_id" => $this->getUser()->getUserIdentifier()
            ]
        );
        $sortiesAuxquellesJeNeSuisPasInscrit = $sortieRepository->findBy(
            [

            ]
        );


        return $this->render('sortie/afficher.html.twig');
    }

}
