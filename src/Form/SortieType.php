<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('infoSortie')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbreInscritsMax')

            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ]);

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($builder) {
                $Sortie = $event->getForm();
                $ville = $Sortie->getData();

                $builder->get('lieu')->resetViewTransformers();
                $builder->add('lieu', EntityType::class, [
                    'class' => Lieu::class,
                    'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $er) use ($ville) {
                        return $er->createQueryBuilder('l')
                            ->where('l.ville = :ville')
                            ->setParameter('ville', $ville);
                    },
                ]);
            }
        )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
