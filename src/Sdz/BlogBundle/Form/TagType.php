<?php
/*{# src/Sdz/BlogBundle/Form/TagType.html.twig */
namespace Sdz\BlogBundle\Form;

//Gestionnaire de formulaire (AT)
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

// Creation de l objet Article Type extence avec le gestionnaire de formulaire AT
class TagType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nom')
        ;
    }

    public function getName()
    {
        return 'sdz_blogbundle_tagtype';
    }
	//Permet de determiner l object par default a utiliser
	public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Sdz\BlogBundle\Entity\Tag',
        );
    }

}
