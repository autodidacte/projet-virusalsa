<?php
// src/Sdz/BlogBundle/Form/ArticleHandler.php

namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Sdz\BlogBundle\Entity\Article;

class ArticleHandler
{
    protected $form;
    protected $request;
    protected $em;

    public function __construct(Form $form, Request $request, EntityManager $em)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
    }

    public function process()
    {
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    public function onSuccess(Article $article)
    {
        $this->em->persist($article);
		// On persiste tous les tags de l'article.
        foreach($article->getTags() as $tag)  
        {
            $this->em->persist($tag);
        }

        $this->em->flush();
    }
}
