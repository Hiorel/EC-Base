<?php

namespace App\Form;

use App\Entity\Screenshots;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ScreenshotsType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, $this->getConfiguration("Lien", "Url du screenshot..."))
            ->add('caption', TextType::class, $this->getConfiguration("Nom", "Nom de l'image'o..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Screenshots::class,
        ]);
    }
}
