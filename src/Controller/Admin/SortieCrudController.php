<?php

namespace App\Controller\Admin;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use ContainerDWbCr4W\getEtatRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function createEntity(string $entityFqcn)
    {

    }

    public function deleteEntity(EntityManagerInterface $entityManager,$entityInstance): void
    {
        if (!$entityInstance instanceof Sortie) return;

        $etatAnnule = $entityManager->find(Etat::class,6);

        $entityInstance->setEtat($etatAnnule);
        $entityInstance->setInfoSortie('Sortie annulée par la modération');
        $entityManager->persist($entityInstance);
        $entityManager->flush();

    }



    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
