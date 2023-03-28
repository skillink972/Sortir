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
        $participants = $participantRepository->findBy(
            [
                'id' => $this->getUser()->getUserIdentifier()
            ]
        );

        return $this->render('participant/MonProfil.html.twig',
            compact('participants'));

    }


    #[Route(
        '/modifier/{participant}',
        name: '_modifier',
        requirements: ['id' => '\d+']
    )]
    public function modifier(
        Participant $participant,
        ParticipantRepository  $participantRepository,
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

            return $this->redirectToRoute('main_index');
        }


        return $this->render('participant/MonProfil.html.twig', compact('participantForm'));

    }

    #[IsGranted('ROLE_USER')]
    #[Route(
        '/supprimer/{participant}',
        name: '_supprimer',
        requirements: ['id' => '\d+']
    )]
    public function supprimer(
        Participant $participant,
        EntityManagerInterface $entityManager
    ): Response {

        $entityManager->remove($participant);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }
}
