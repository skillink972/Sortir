<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/CreeSortie', name: 'app_CreeSortie')]
    public function nouveau(): Response
    {
        $Sortie = new SortieController();
        $SortieForm = $this->createForm(SortieType::class, $Sortie);
        $SortieForm->handleRequest();
        if ($SortieForm->isSubmitted() && $SortieForm->isValid()) {
            try {
                $Sortie->setNom(getNom());
            } catch (\Exception $exception) {
                $this->addFlash('echec', 'Le souhait n\'a pas été inséré');

                return $this->redirectToRoute('app_CreeSortie');
            }


            return $this->render('cree_sortie/index.html.twig', [
                'controller_name' => 'SortieController',
            ]);
        }
    }
}
