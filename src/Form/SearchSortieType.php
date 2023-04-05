<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\PropertySearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,
                [
                    'label'         => 'Campus',
                    'class'         => Campus::class,
                    'choice_label'  => 'nom'
                ])
            ->add('motCle')
            ->add('dateMin', DateType::class,
                [
                    'label'         => 'Entre',
                    'required'      => false,
                    'widget'        => 'single_text'
                ])
            ->add('dateMax',DateType::class,
                [
                    'label'         => 'et',
                    'required'      => false,
                    'widget'        => 'single_text'
                ])
            ->add('organisateur', CheckboxType::class,
                [
                    'label'         => 'Sorties dont je suis l\'organisateur/rice',
                    'attr'          => array('checked'  => 'checked'),
                    'required'      => false
                ])
            ->add('inscrit', CheckboxType::class,
                [
                    'label'         => 'Sorties auxquelles je suis inscrit/e',
                    'attr'          => array('checked'  => 'checked'),
                    'required'      => false
                ])
            ->add('nonInscrit', CheckboxType::class,
                [
                    'label'         => 'Sorties auxquelles je ne suis pas inscrit/e',
                    'attr'          => array('checked'  => 'checked'),
                    'required'      => false
                ])
            ->add('passees', CheckboxType::class,
                [
                    'label'         => 'Sorties passées',
                    'required'      => false
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
        ]);
    }
}
