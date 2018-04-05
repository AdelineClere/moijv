<?php

namespace App\Form;

use App\DataTransformers\TagTransformer;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $tagTransformer;
    
    public function __construct(TagTransformer $tagTransformer) 
    {
        $this->tagTransformer = $tagTransformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description') // pas besoin de : , TextareaType::class) => Sfy sait tout seul
            ->add('image', FileType::class,[
                'required' => false  // = champs pas requis = car en mode edit on veut éviter qu'il demande à recharger une img
            ])     
            
            // ajouter champ au formulaire (col ds table product va s'appeler tags / 
            // tags fait réf à private tags de Entity Product
            // tags = liste (collec°) qu'on va aff. en string grâce à modelTransformer
            ->add('tags',TextType::class)
            ->get('tags')
                ->addModelTransformer($this->tagTransformer) //(tagTransformer transform ds les 2 sens)
                    
            ;
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
