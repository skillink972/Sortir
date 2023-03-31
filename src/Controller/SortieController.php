<?php

namespace App\Controller;
use App\Entity\Participant;
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
use function Sodium\add;

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
        $sortiesO = $sortieRepository->findSearch1($search);
        $sortiesI = $sortieRepository->findSearch2($search);
        $sortiesN = $sortieRepository->findSearch3($search);
        dump($sortiesN);
        $sortiesP = $sortieRepository->findSearch4($search);
        $sortiesOI = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch2($search));
        $sortiesON = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch3($search));
        $sortiesOP = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch4($search));
        $sortiesIN = array_merge($sortieRepository->findSearch2($search),$sortieRepository->findSearch3($search));
        $sortiesIP = array_merge($sortieRepository->findSearch2($search),$sortieRepository->findSearch4($search));
        $sortiesNP = array_merge($sortieRepository->findSearch3($search),$sortieRepository->findSearch4($search));
        $sortiesOIN = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch2($search), $sortieRepository->findSearch3($search));
        $sortiesOIP = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch2($search), $sortieRepository->findSearch4($search));
        $sortiesONP = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch3($search), $sortieRepository->findSearch4($search));
        $sortiesINP = array_merge($sortieRepository->findSearch2($search),$sortieRepository->findSearch3($search), $sortieRepository->findSearch4($search));
        $sortiesOINP = array_merge($sortieRepository->findSearch1($search),$sortieRepository->findSearch2($search),$sortieRepository->findSearch3($search), $sortieRepository->findSearch4($search));

        $sorties = $sortiesOIN;
        if ($search->isOrganisateur()) {
            if ($search->isInscrit()) {
                if ($search->isNonInscrit()) {
                    if ($search->isPassees()) {
                        $sorties = $sortiesOINP;
                    }
                    else {
                        $sorties = $sortiesOIN;
                    }
                }
                else {
                    if ($search->isPassees()) {
                        $sorties = $sortiesOIP;
                    }
                    else {
                        $sorties = $sortiesOI;
                    }
                }
            }
            else {
                if ($search->isNonInscrit()) {
                    if ($search->isPassees()) {
                        $sorties = $sortiesONP;
                    }
                    else {
                        $sorties = $sortiesON;
                    }
                }
                else {
                    if ($search->isPassees()) {
                        $sorties = $sortiesOP;
                    }
                    else {
                        $sorties = $sortiesO;
                    }
                }
            }
        }
        else {
            if ($search->isInscrit()) {
                if ($search->isNonInscrit()) {
                    if ($search->isPassees()) {
                        $sorties = $sortiesINP;
                    }
                    else {
                        $sorties = $sortiesIN;
                    }
                }
                else {
                    if ($search->isPassees()) {
                        $sorties = $sortiesIP;
                    }
                    else {
                        $sorties = $sortiesI;
                    }
                }
            }
            else {
                if ($search->isNonInscrit()) {
                    if ($search->isPassees()) {
                        $sorties = $sortiesNP;
                    }
                    else {
                        $sorties = $sortiesN;
                    }
                }
                else {
                    if ($search->isPassees()) {
                        $sorties = $sortiesP;
                    }
                    else {
                        $sorties = $sortiesI;
                    }
                }
            }
        }

        return $this->render('sortie/lister.html.twig',
            compact('searchForm', 'sorties')
        );
    }

    #[IsGranted('ROLE_USER')]
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

    #[IsGranted('ROLE_USER')]
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

    #[IsGranted('ROLE_USER')]
    #[Route('/annuler/{sortie}',
        name: '_annuler',
        requirements: ['id' => '\d+'])]
    public function annuler(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager

    ): Response
    {
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }


        if($sortie->getOrganisateur() === $this->getUser() and ($sortie->getEtat()->getId() == 1 or $sortie->getEtat()->getId() == 2  or $sortie->getEtat()->getId() == 3 )) {
            try {
                //TODO il ne faut pas supprimer la sortie mais set son Etat sur 6
                $entityManager->remove($sortie);
                $entityManager->flush();
                $this->addFlash('msgSucces', "Votre sortie a bien été supprimée.");
                return $this->redirectToRoute('sortie_lister');
            } catch (\Exception $e) {
                $this->addFlash('msgError', "Votre sortie n'a pas pu être annulée.");
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            }
        }

        return $this->render('sortie/details.html.twig',compact('sortie'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/modifier/{sortie}',
        name: '_modifier',
        requirements: ['id' => '\d+'])]
    public function modifier(
        Sortie                 $sortie,
        Request                $request,
        EntityManagerInterface $entityManager

    ): Response
    {
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }


        if($sortie->getOrganisateur() === $this->getUser() and ($sortie->getEtat()->getId() == 1 or $sortie->getEtat()->getId() == 2  or $sortie->getEtat()->getId() == 3 ) ) {
            try {
                $SortieForm = $this->createForm(SortieType::class, $sortie);
                $SortieForm->handleRequest($request);


                if ($SortieForm->isSubmitted() && $SortieForm->isValid()) {
                    try {
                        //TODO mettre le même formulaire que celui de la méthode créer
                        //TODO aller dans le TWIG lister et remplacer le lien "annuler" par "modifier"
                        //TODO mettre un bouton/lien avec la route annuler sortie sur le TWIG modifier_une_sortie

                    } catch (\Exception $exception) {
                        $this->addFlash('echec', 'La sortie n\'a pas pu être modifiée :(');
                        return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
                    }

                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('msgSucces', "Votre sortie a bien été modifiée.");
                    return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            }
            }
            catch (\Exception $e) {
                $this->addFlash('msgError', "Votre sortie n'a pas pu être modifiée.");
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            }
        }
        return $this->render('sortie/details.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/participer/{sortie}', name: '_participer')]
    public function participer(
        Sortie                 $sortie,
        ParticipantRepository  $participantRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = $participantRepository->findOneBy(
            [
                "email" => $this->getUser()->getUserIdentifier()
            ]
        );
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }
        if ($sortie->getParticipants()->count() < $sortie->getNbreInscritsMax() && $sortie->getEtat()->getId() == 2) {
            try {
                $sortie->addParticipant($participant);
                $entityManager->persist($sortie);
                $entityManager->flush($sortie);
                $this->addFlash('msgSucces', "Vous êtes inscrit à la sortie.");
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('msgError', "Vous ne pouvez pas vous inscrire à cette sortie car le nombre maximum de participants est atteint.");
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            }
        } else {
            $this->addFlash('msgError', "Vous ne pouvez pas vous inscrire à cette sortie car le nombre maximum de participants est atteint.");
        }
        return $this->render('sortie/lister.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/seDesister/{sortie}', name: '_seDesister')]
    public function seDesister(
        Sortie                 $sortie,
        ParticipantRepository  $participantRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = $participantRepository->findOneBy(
            [
                "email" => $this->getUser()->getUserIdentifier()
            ]
        );
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }
        foreach ($participant->getSortiesParticipees() as $sortieParticipee) {
            if ($sortieParticipee === $sortie) {
                try {
                    $sortie->removeParticipant($participant);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('msgSucces', "Vous êtes désinscrit de la sortie.");
                    return $this->redirectToRoute('sortie_lister');
                } catch (\Exception $e) {
                    $this->addFlash('msgError', "Erreur lors de la désinscription");
                    return $this->redirectToRoute('sortie_lister');
                }
            }
        }
        return $this->render('sortie/lister.html.twig');
    }

}