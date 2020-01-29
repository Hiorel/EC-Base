<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Pseudo (Obligatoire)", "Votre pseudo..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email (Obligatoire)", "Votre adresse email..."))
            ->add('avatar', FileType::class, [
                  'label' => 'Avatar (Optionnel / Format PNG ou JPG (Poids 5 Mo maximum))',
                  'required' => false,
                  'attr' => ['placeholder' => "Aucun fichier",
                'class' => 'form-control mb-3',
            ],

                  // unmapped fields can't define their validation using annotations
                  // in the associated entity, so you can use the PHP constraint classes
                  'constraints' => [
                      new File([
                          'maxSize' => '5120k',
                          'mimeTypes' => [
                              'image/jpeg',
                              'image/png',
                          ],
                          'mimeTypesMessage' => 'Veuillez insérer un format d\'image correcte',
                      ])
                  ],
            ])
            ->add('hash', PasswordType::class, [
                  'label' => 'Mot de passe',
                  'attr' => ['placeholder' => "Choisissez un bon mot de passe...",
                'class' => 'form-control mb-3',
                'autocomplete' => 'off',
            ]])
            ->add('passwordConfirm', PasswordType::class, [
                'label' => 'Confirmation de mot de passe',
                'attr' => ['placeholder' => "Veuillez confirmer votre mot de passe",
              'class' => 'form-control mb-3',
              'autocomplete' => 'off',
          ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
