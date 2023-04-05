<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',null,[
                    'label' => 'Pseudo : ',
                'attr' => [
                    'class' => 'form-control',
                ],

                    ])

            ->add('prenom',null,[
                'label' => 'Prénom : ',
                'attr' => [
                    'class' => 'form-control',
                ],

            ])
            ->add('nom',null,[
                'label' => 'Nom : ',
                'attr' => [
                    'class' => 'form-control',
                ],

            ])
            ->add('telephone',null,[
                'label' => 'Téléphone : ',
                'attr' => [
                    'class' => 'form-control',
                ],

            ])
            ->add('email',null,[
                'label' => 'Email : ',
                'attr' => [
                    'class' => 'form-control',
                ],

            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Mot de passe : ',
                    'attr' => [
                        'class' => 'form-control',
                    ],],
                'second_options' => ['label' => 'Confirmation mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ],],
                'required' => true,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('campus',EntityType::class,
            [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'expanded' => true,
                'label' => 'Campus : '
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil (JPEG ou PNG)',
                'mapped' => false,
                'required' => false,

            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
