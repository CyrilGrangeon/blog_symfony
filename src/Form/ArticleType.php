<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un titre pour l\'article'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le titre de l\'article doit contenir au moins {{ limit }} caractères.',
                        'max' => 75,
                        'maxMessage' => 'Le titre de l\'article doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ]
                
            ])
            
            ->add('content',TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un contenu pour l\article'
                    ]),
                    new Length([
                        'min' => 50,
                        'minMessage' => 'l\'article doit contenir au moins {{ limit }} caractères.',
                        
                    ]),
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une categorie',
            ])
            ->add('isPublished', CheckboxType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
