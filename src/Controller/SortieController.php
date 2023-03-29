<?php

namespace App\Controller;
use App\Entity\PropertySearch;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie', name:'sortie')]
class SortieController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/lister', name: '_lister')]
    public function lister(
        Request               $request,
        SortieRepository      $sortieRepository,
        ParticipantRepository $participantRepository
    ): Response
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
        return $this->render('sortie/lister.html.twig',
            compact('searchForm', 'sorties')
        );
    }

    #[Route('/creer', name: '_creer')]
    public function nouveau(
        EntityManagerInterface $entityManager,
        ParticipantRepository  $participantRepository,
        Request                $request,
        EtatRepository         $etatRepository,
    ): Response
    {
        $utilisateurConnecte = $participantRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $Sortie = new Sortie();
        $SortieForm = $this->createForm(SortieType::class, $Sortie);
        $SortieForm->handleRequest($request);
        if ($SortieForm->isSubmitted() && $SortieForm->isValid()) {
            try {
                $Sortie->setNom(($Sortie->getNom()));
                $Sortie->setInfoSortie(($Sortie->getInfoSortie()));
                $Sortie->setDateHeureDebut(($Sortie->getDateHeureDebut()));
                $Sortie->setDuree(($Sortie->getDuree()));
                $Sortie->setDateLimiteInscription(($Sortie->getDateLimiteInscription()));
                $Sortie->setNbreInscritsMax(($Sortie->getNbreInscritsMax()));
                $Sortie->setLieu(($Sortie->getLieu()));
                $etat = $etatRepository->findOneBy(["libelle" => 'en cours']);
                $Sortie->setEtat($etat);
                $Sortie->setCampus($utilisateurConnecte->getCampus());
                $organistateur = $participantRepository->findOneBy(["nom" => $utilisateurConnecte->getnom()]);
                $Sortie->setOrganisateur($organistateur);
                $entityManager->persist($Sortie);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('echec', 'La sortie n\'a pas pu être crée :(');
                return $this->redirectToRoute('sortie_creer');
            }
            return $this->render('sortie/creer.html.twig',
                compact('SortieForm'));
        }
        return $this->render('sortie/creer.html.twig',
            compact('SortieForm'));
    }

    #[Route('/details/{sortie}', name: '_details')]
    public function details(
        Sortie $sortie
    ): Response
    {
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }
        return $this->render('sortie/details.html.twig',
            compact('sortie')
        );
    }
}
