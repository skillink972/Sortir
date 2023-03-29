<?php

namespace App\Controller;
use App\Entity\PropertySearch;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie', name:'sortie')]
class SortieController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/afficher', name: '_afficher')]
    public function afficher(
        Request $request,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository
    ):Response
    {
        $search = new PropertySearch();
        $userConnecte = $participantRepository->findOneBy(
            [
                'id' => $this->getUser()
            ]
        );
        $search->setCampus($userConnecte->getCampus());
        $search->setUser($userConnecte);
        $searchForm = $this->createForm(SearchSortieType::class, $search);
        $searchForm->handleRequest($request);
        $sorties = $sortieRepository->findSearch($search);
        return $this->render('sortie/afficher.html.twig',
            compact('searchForm', 'sorties')
        );
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
