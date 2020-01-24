<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LodestoneType extends AbstractType
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
        ->add('title', TextType::class, $this->getConfiguration("Nom de l'article", "Titre de l'article..."))
        ->add('urlExt', UrlType::class, $this->getConfiguration("URL de Lodestone", "Url de l'article..."))
        ->add('cover', FileType::class, [
            'label' => 'Couverture (Format PNG ou JPG (Poids 5 Mo maximum))',
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
          ->get('cover')->addModelTransformer(new CallBackTransformer(
            function($cover) {
                return null;
            },
            function($cover) {
                return $cover;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
