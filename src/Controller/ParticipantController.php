<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

        Participant $participant,
        ParticipantRepository  $participantRepository

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

            $profilePicture = $participantForm->get('profilePicture')->getData();

            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();


                try {
                    $profilePicture->move(
                        $this->getParameter('photos_utilisateurs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $participant->setPhoto($newFilename);
            }

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
        EntityManagerInterface  $entityManager,
        SortieRepository $sortieRepository,
    ): Response {

        $session = new \Symfony\Component\HttpFoundation\Session\Session();
        $session->invalidate();

        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('msgSucces','Votre compte utilisateur a bien été supprimé ');

        return $this->redirectToRoute('main_index');
    }
}
