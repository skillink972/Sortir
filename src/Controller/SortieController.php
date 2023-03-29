<?php

namespace App\Controller;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/CreeSortie', name: 'CreeSortie')]
    public function nouveau(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        Request $request,
        EtatRepository $etatRepository,
    ): Response
    {
$utilisateurConnecte = $participantRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $Sortie = new Sortie();
        $SortieForm = $this->createForm(SortieType::class, $Sortie);
        $SortieForm->handleRequest($request);
if($SortieForm->isSubmitted() && $SortieForm->isValid()) {
    try {
        $Sortie->setNom(($Sortie->getNom()));
        $Sortie->setInfoSortie(($Sortie->getInfoSortie()));
        $Sortie->setDateHeureDebut(($Sortie->getDateHeureDebut()));
        $Sortie->setDuree(($Sortie->getDuree()));
        $Sortie->setDateLimiteInscription(($Sortie->getDateLimiteInscription()));
        $Sortie->setNbreInscritsMax(($Sortie->getNbreInscritsMax()));
        $Sortie->setLieu(($Sortie->getLieu()));
        $etat=  $etatRepository->findOneBy(["libelle"=>'en cours']);
        $Sortie->setEtat($etat);
        $Sortie->setCampus($utilisateurConnecte->getCampus());
        $organistateur = $participantRepository->findOneBy(["nom"=>$utilisateurConnecte->getnom()]);
        $Sortie->setOrganisateur($organistateur);
        $entityManager->persist($Sortie);
        $entityManager->flush();
    }catch (\Exception $exception) {
        $this->addFlash('echec', 'La sortie n\'a pas pu être crée :(');

        return $this->redirectToRoute('CreeSortie');
    }
}
        return $this->render('cree_sortie/index.html.twig',
            compact('SortieForm')
        );
    }

}
