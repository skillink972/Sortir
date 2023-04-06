<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville')]
class VilleController extends AbstractController
{
    #[Route('/creer', name: '_creer')]
    public function creer(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()){
            try {
                $entityManager->persist($ville);
                $entityManager->flush();
                $this->addFlash('msgSucces', "La ville a été ajoutée avec succès !");
                return $this->redirectToRoute('lieu_creer');
            } catch (\Exception $exception) {
                $this->addFlash('msgError', "La ville n'a pas pu être ajoutée.");
                return $this->redirectToRoute('ville_creer');
            }
        }
        return $this->render("ville/creer.html.twig",
            compact('villeForm', 'ville'));
    }
}
