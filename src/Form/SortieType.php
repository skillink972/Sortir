<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function __construct(
        private Security $security,
        private LieuRepository $lieuRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,
                [
                    'label'         => 'Nom de la sortie : ',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
            ->add('dateHeureDebut', DateTimeType::class,
                [
                    'label'         => 'Date et heure de la sortie : ',
                    'widget'        => 'single_text'
                ])
            ->add('duree', IntegerType::class,
                [
                    'label'         => 'Durée(en minutes): ',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
            ->add('dateLimiteInscription', DateTimeType::class,
                [
                    'label'         => 'Date limite d\'inscription : ',
                    'widget'        => 'single_text'
                ])
            ->add('nbreInscritsMax', IntegerType::class,
                [
                    'label'         => 'Nombre de places : ',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
            ->add('infoSortie', TextType::class,
                [
                    'label'         => 'Description et infos : ',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'required'      => false
                ])
            ->add('campus', EntityType::class,
                [
                    'label'         => 'Campus : ',
                    'class'         =>  Campus::class,
                    'choice_label'  => 'nom',
                    'query_builder' => function(CampusRepository $campusRepository) {
                        $user = $this->security->getUser();
                        return $campusRepository->createQueryBuilder('c')
                            ->select('c')
                            ->andWhere('c.id LIKE :campus')
                            ->setParameter('campus', $user->getCampus()->getId());
                    }
                ])
            ->add('ville', EntityType::class,
                [
                    'label'         => 'Ville : ',
                    'class'         => Ville::class,
                    'mapped'        => false,
                    'choice_label'  => 'nom',
                    'query_builder' => function(VilleRepository $villeRepository) {
                        $user = $this->security->getUser();
                        return $villeRepository->createQueryBuilder('v')
                            ->select('v')
                            ->andWhere('v.codePostal LIKE :cp')
                            ->setParameter('cp', "{$user->getCampus()->getDepartement()}%");
                    },
                    'placeholder'   => 'Choisissez une ville',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $sortieForm = $event->getForm();
                    $sortieForm->add('lieu', EntityType::class,
                        [
                            'label'         => 'Lieu : ',
                            'class'         => Lieu::class,
                            'choices'       => [],
                            'choice_label'  => 'nom',
                        ]);
                });

        $builder
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $lieu = $event->getData()['lieu'];
                    $objetLieu = $this->lieuRepository->findOneBy(['id'=>$lieu]);
                    if ($lieu) {
                        $form->add('lieu', EntityType::class,
                            [
                                'label'         => 'Lieu : ',
                                'class'         => Lieu::class,
                                'choices'       => [$objetLieu],
                                'choice_label'  => 'nom'
                            ]);
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'            => Sortie::class,
            'allow_extra_fields'    => true
        ]);
    }


}
