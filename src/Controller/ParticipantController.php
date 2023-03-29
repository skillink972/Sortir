<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_USER')]
#[Route('/participant', name: 'participant')]
class ParticipantController extends AbstractController
{
    #[Route('/MonProfil', name: '_MonProfil')]
    public function _MonProfil(

        ParticipantRepository  $participantRepository,

    ): Response
    {
        $participant = $participantRepository->findBy(
            [
                'id' => $this->getUser()->getUserIdentifier()
            ]
        );

        return $this->render('participant/MonProfil.html.twig',
            compact('participant'));

    }

    #[Route(
        '/AutreProfil/{participant}',
        name: '_Autreprofil'
    )]
    public function _AutreProfil(

        ParticipantRepository  $participantRepository,
        Participant $participant

    ): Response
    {
        $autreProfil = $participantRepository->find($participant);
        if (!$autreProfil) {
            throw $this->createNotFoundException('Cette personne n\'existe pas.');
        }

        return $this->render('participant/AutreProfil.html.twig',
            compact('autreProfil')
        );

    }

    #[Route('/confirmationSuppression', name: '_ConfirmationSuppression')]
    public function _ConfirmationSuppression(

        ParticipantRepository  $participantRepository,

    ): Response
    {
        $participant = $participantRepository->findBy(
            [
                'id' => $this->getUser()->getUserIdentifier()
            ]
        );

        return $this->render('participant/SupprimerCompte.html.twig',
            compact('participant'));

    }


    #[Route(
        '/modifier/{participant}',
        name: '_modifier',
        requirements: ['id' => '\d+']
    )]
    public function modifier(
        Participant            $participant,
        Request                $request,
        EntityManagerInterface $entityManager

    ): Response
    {
//      $participantConnecte = $participantRepository->findOneBy(["id" => $this->getUser()->getUserIdentifier()]);
        $participantForm = $this->createForm(RegistrationFormType::class, $participant);
        $participantForm->handleRequest($request);


        if ($participantForm->isSubmitted() && $participantForm->isValid() ) {

            $participant->setPseudo($participant->getPseudo());
            $participant->setPrenom($participant->getPrenom());
            $participant->setNom($participant->getNom());
            $participant->setTelephone($participant->getTelephone());
            $participant->setEmail($participant->getEmail());
            $participant->setCampus($participant->getCampus());
            $participant->setPhoto($participant->getPhoto());


            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_MonProfil');
        }

        return $this->render('participant/ModifierMonProfil.html.twig', compact('participantForm'));

    }


    #[Route(
        '/supprimer/{participant}',
        name: '_supprimer',
       // requirements: ['participant' => '\d+']
    )]
    public function supprimer(
        Participant             $participant,
        EntityManagerInterface  $entityManager
    ): Response {

        $entityManager->remove($participant);
        $entityManager->flush();

        return $this->render('main/index.html.twig');
    }
}
