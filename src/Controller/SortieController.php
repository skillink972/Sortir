<?php

namespace App\Controller;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\PropertySearch;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Form\SuppressionSortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use function Sodium\add;

#[Route('/sortie', name:'sortie')]
class SortieController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/lister', name: '_lister')]
    public function lister(
        Request               $request,
        SortieRepository      $sortieRepository,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $now = new \DateTime;
        dump($now);
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
            compact('searchForm', 'sorties', 'now')
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recupererLieux/{ville}', name: '_recupererLieux')]
    public function recupererLieux (
        LieuRepository $lieuRepository,
        string $ville,
        SerializerInterface $serializer
    ) : Response
    {
        $lieux = $lieuRepository->findBy(
            [
                'ville' => $ville
            ]
        );
        $productSerialized = $serializer->serialize($lieux, 'json', ['groups' => ['group']]);

        return new Response($productSerialized);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/creer', name: '_creer')]
    public function creer(
        EntityManagerInterface  $entityManager,
        ParticipantRepository   $participantRepository,
        Request                 $request,
        EtatRepository $etatRepository
    ): Response
    {
        $utilisateurConnecte = $participantRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            try {
                $organistateur = $participantRepository->findOneBy(["id" => $utilisateurConnecte->getId()]);
                $sortie->setOrganisateur($organistateur);
                $etat = $etatRepository->findOneBy(['id'=>2]);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('msgSucces', 'La sortie a été créée avec succès !');
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('msgError', 'La sortie n\'a pas pu être créée :( '.$exception);
                return $this->render('sortie/creer.html.twig');
            }
        }
        return $this->render('sortie/creer.html.twig',
            compact('sortieForm', 'sortie'));
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
        EntityManagerInterface $entityManager,
        Request                $request,
        EtatRepository         $etatRepository

    ): Response
    {
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas');
        }


        if($sortie->getOrganisateur() === $this->getUser() and ($sortie->getEtat()->getId() == 1 or $sortie->getEtat()->getId() == 2  or $sortie->getEtat()->getId() == 3 )) {

            try {
                $motifSuppressionForm = $this->createForm(SuppressionSortieType::class);
                $motifSuppressionForm->handleRequest($request);

                if ($motifSuppressionForm->isSubmitted() && $motifSuppressionForm->isValid()) {

                    $sortie->setEtat($etatRepository->find(6));
                    $sortie->setInfoSortie($motifSuppressionForm->get('motifAnnulation')->getData());
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('msgSucces', "Votre sortie a bien été annulée. Elle reste consultable 1 mois sur le site");
                    return $this->redirectToRoute('sortie_lister');
                }
            } catch (\Exception $e) {
                $this->addFlash('msgError', "Votre sortie n'a pas pu être annulée.");
                return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
            }
            return $this->render('sortie/supprimer.html.twig', compact('motifSuppressionForm', 'sortie'));
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

            $SortieForm = $this->createForm(SortieType::class, $sortie);
            $SortieForm->handleRequest($request);


            if ($SortieForm->isSubmitted() && $SortieForm->isValid()) {
                try {
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('msgSucces', 'La sortie a été modifiée avec succès !');
                    return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
                } catch (\Exception $exception) {
                    $this->addFlash('echec', 'La sortie n\'a pas pu être modifiée :(');
                    return $this->redirectToRoute('sortie_details', ['sortie' => $sortie->getId()]);
                }

            }
            return $this->render('sortie/modifier.html.twig', compact('SortieForm', 'sortie'));
        }
            return $this->render('sortie/modifier.html.twig', compact( 'sortie'));
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