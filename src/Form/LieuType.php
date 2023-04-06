<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,
                [
                    'label'         => 'Nom du lieu : '
                ])
            ->add('rue', TextType::class,
                [
                    'label'         => 'Rue : '
                ])
            ->add('latitude', TextType::class,
                [
                    'label'         => 'Latitude : ',
                    'required'      => false
                ])
            ->add('longitude', TextType::class,
                [
                    'label'         => 'Longitude : ',
                    'required'      => false
                ])
            ->add('ville', EntityType::class,
                [
                    'label'         => 'Ville : ',
                    'class'         => Ville::class,
                    'choice_label'  => 'nom',
                    'query_builder' => function (VilleRepository $villeRepository) {
                        $user = $this->security->getUser();
                        return $villeRepository->createQueryBuilder('v')
                            ->select('v')
                            ->andWhere('v.codePostal LIKE :cp')
                            ->setParameter('cp', "{$user->getCampus()->getDepartement()}%");
                    },
                    'placeholder'   => 'Choisissez une ville'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
