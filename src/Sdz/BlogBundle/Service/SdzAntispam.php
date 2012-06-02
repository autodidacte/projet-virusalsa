<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php

namespace Sdz\BlogBundle\Service;

/**
 * Un anti-spam simple pour Symfony2.
 *
 * @author Leglopin
 */
class SdzAntispam extends \Twig_Extension
{
    // La méthode getName(), obligatoire
    public function getName()
    {
        return 'SdzAntispam';
    }
	
    // La méthode getFunctions(), qui retourne un tableau avec les fonctions qui peuvent être appelées depuis cette extension
    public function getFunctions()
    {
        return array(
            'antispam_check' => new \Twig_Function_Method($this, 'isSpam') 
        );

        // 'antispam_check' est le nom de la fonction qui sera disponible sous Twig
        // 'new \Twig_Function_Method($this, 'isSpam') ' est la façon de dire que cette fonction va exécuter notre méthode isSpam ci-dessous
    }
	
    /**
     * Vérifie si le texte est un spam ou non.
     * Un texte est considéré comme spam à partir de 3 liens
     * ou adresses e-mails dans son contenu.
     * 
     * @param string $text
     */
    public function isSpam($text)
    {
        if( ($this->countLinks($text) + $this->countMails($text)) >= 3 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * Compte les URL de $text.
     * 
     * @param string $text
     */
    private function countLinks($text)
    {
        preg_match_all(
            '#(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i',
            $text,
            $matches);
        
        return count($matches[0]);
    }
    
    /**
     * Compte les e-mails de $text.
     * 
     * @param string $text
     */
    private function countMails($text)
    {
        preg_match_all(
            '#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}#i',
            $text,
            $matches);
        
        return count($matches[0]);
    }
}

