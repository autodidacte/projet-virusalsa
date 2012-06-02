<?php
/*{# src/Sdz/BlogBundle/Form/ArticleType.html.twig */
namespace Sdz\BlogBundle\Form;

//Gestionnaire de formulaire (AT)
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

// Creation de l objet Article Type extence avec le gestionnaire de formulaire AT
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('titre')
            
			->add('auteur')
            /*
             * Rappel :
             ** - 1er argument : nom du champ ;
             ** - 2e argument : type du champ ;
             ** - 3e argument : tableau d'options du champ.
             */
                
            ->add('contenu', 'textarea', array(
        'attr' => array(
            'class' => 'tinymce',
            'data-theme' => 'advanced' // simple, advanced, bbcode
        )
    ))
                
            ->add('tags', 'collection', array('type'      => new TagType,
                                              'prototype' => true,
                                              'allow_add' => true))
                
        ;
    }


    public function getName()
    {
        return 'sdz_blogbundle_articletype';
    }
	//Permet de determiner l object par default a utiliser
	public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Sdz\BlogBundle\Entity\Article',
        );
    }

}
