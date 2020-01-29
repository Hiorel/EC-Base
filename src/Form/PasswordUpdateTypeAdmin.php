<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateTypeAdmin extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => ['placeholder' => "Donnez votre nouveau mot de passe ...",
              'class' => 'form-control mb-3',
              'autocomplete' => 'off',
          ]])
              ->add('confirmPassword', PasswordType::class, [
              'label' => 'Confirmation de votre nouveau mot de passe',
              'attr' => ['placeholder' => "Confirmez votre nouveau mot de passe ...",
            'class' => 'form-control mb-3',
            'autocomplete' => 'off',
        ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
