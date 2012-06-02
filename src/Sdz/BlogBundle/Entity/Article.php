<?php
// src/Sdz/BlogBundle/Entity/Article.php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
// Définit le namespace pour les annotations de validation.
use Symfony\Component\Validator\Constraints as Assert;
// Utilisation de l array collection
use Doctrine\Common\Collections\ArrayCollection;
// Lier une entite
use Sdz\BlogBundle\Entity\Tag;
// On rajoute ce use pour le context :
use Symfony\Component\Validator\ExecutionContext;
// Contrainte d avoir une valeur unique dans une entity
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Sdz\BlogBundle\Entity\Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\ArticleRepository")
 *
 * @Assert\Callback(methods={"contenuValide"})
 * @UniqueEntity(fields="auteur", message="Un auteur existe déjà avec ce nom.")
 */
class Article
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date $date
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="string", length=255)
	 * @Assert\NotBlank(message="Obligatoire")
     * @Assert\MinLength(10)
     */
    private $titre;
    
    /**
     * @var text $contenu
     *
     * @ORM\Column(name="contenu", type="text")
     * @Assert\NotBlank(message="Obligatoire")
     */
    private $contenu;
    
    /**
     * @var string $auteur
     *
     * @ORM\Column(name="auteur", type="string", length=255,unique=true)
	 * @Assert\NotBlank(message="Obligatoire")
     * @Assert\MinLength(2)
     */
    private $auteur;
    
    /**
     * @ORM\ManyToMany(targetEntity="Sdz\BlogBundle\Entity\Tag")
	 * @Assert\NotBlank(message="Obligatoire")
     * @Assert\Valid()
     */
    private $tags;



    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->date = new \Datetime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set titre
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param text $contenu
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    /**
     * Get contenu
     *
     * @return text
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Add tags
     *
     * @param Sdz\BlogBundle\Entity\Tag $tags
     */
    public function addTag(\Sdz\BlogBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }
	
	public function contenuValide(ExecutionContext $context)
	{
		$mots_interdits = array('échec', 'abandon');
		
		// On vérifie que le contenu ne contient pas l'un des mots
		if(preg_match('#'.implode('|', $mots_interdits).'#', $this->getContenu()))
		{
			// On dit où est l'erreur avec le ".contenu"
			$propertyPath = $context->getPropertyPath().'.contenu';
			$context->setPropertyPath($propertyPath);
			
			// La règle est violée, on définit l'erreur et son message
			$context->addViolation('Contenu invalide car il contient un mot interdit.', array(), null);
		}
	}


	
}