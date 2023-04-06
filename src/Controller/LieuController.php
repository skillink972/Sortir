<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/creer', name: '_creer')]
    public function creer(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()){
            try {
                $entityManager->persist($lieu);
                $entityManager->flush();
                $this->addFlash('msgSucces', "Le lieu a été ajouté avec succès !");
                return $this->redirectToRoute('sortie_creer');
            } catch (\Exception $exception) {
                $this->addFlash('msgError', "Le lieu n'a pas pu être ajouté.");
                return $this->redirectToRoute('lieu_creer');
            }
        }
        return $this->render("lieu/creer.html.twig",
            compact('lieuForm', 'lieu'));
    }
}
