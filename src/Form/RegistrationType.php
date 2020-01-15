<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
                'class' => 'form-control mb-3'
             ]
         ], $options);
    }

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
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Choississez un bon mot de passe..."))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot passe", "Veuillez confirmer votre mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
