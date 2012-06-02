<?php
// Bundle ou il agira
namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Objet contenant les donnees de la bdd et les methode get et set des donnees
use Sdz\BlogBundle\Entity\Article;
// Formulaire d enregistrement des article
use Sdz\BlogBundle\Form\ArticleType;

/**
 * Article controller.
 *
 */
 // Objet Article (framework) extence dans le controller
class ArticleController extends Controller
{
    /**
     * Lists all Article entities.
     *
     */
    public function indexAction()
    {

	// Recuperation des entites (objet // contenu de la bdd) dans $em par la methode suivante
        $em = $this->getDoctrine()->getEntityManager();

    // Recuperation des donnees dans $entites pour affichage avec la methode getRepository(adresseBundle:nom-bdd)
	// Recuperation de toutes les infos par la methode findAll()
		$entities = $em->getRepository('SdzBlogBundle:Article')->findAll();
    // Requette personnelle myFindAll() construite dans entity article.
		$liste_articles = $this->getDoctrine()
                           ->getEntityManager()
                           ->getRepository('SdzBlogBundle:Article')
                           ->myFindAll();
		   
        return $this->render('SdzBlogBundle:Article:index.html.twig', array(
            'entities' => $entities,
			'liste_articles' => $liste_articles
        ));
    }
	
    /**
     * Finds and displays a Article entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('SdzBlogBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
 //Requette personnelle myFindOne construite dans entity article
		$id_article = $this->getDoctrine()
                           ->getEntityManager()
                           ->getRepository('SdzBlogBundle:Article')
                           ->myFindOne($id);
        return $this->render('SdzBlogBundle:Article:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			'id_article'=>$id_article

        ));
    }
/*
	//affichage d une jointure
	public function listeAction()
{
    $liste_articles = $this->getDoctrine()
                           ->getEntityManager()
                           ->getRepository('SdzBlogBundle:Article')
                           ->getArticleAvecCommentaires();

    foreach($liste_articles as $article)
    {
        $article->getCommentaires(); // Ne déclenche pas de requête : les commentaires sont déjà chargés !
                                     // Vous pourriez faire une boucle dessus pour les afficher tous.
    }

    // suite render()
}
*/
    /**
     * Displays a form to create a new Article entity.
     *
     */
    public function newAction()
    {
        $entity = new Article();
        $form   = $this->createForm(new ArticleType(), $entity);

        return $this->render('SdzBlogBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Article entity.
     *
     */
    public function createAction()
    {
        // declaration 
		$entity  = new Article();
        //Recuperation de la requette POST 
		$request = $this->getRequest();
        $form    = $this->createForm(new ArticleType(), $entity);
        $form->bindRequest($request);

        // si les donnes de la requette s sont valide, en enregistre les entites (objet)
		if ($form->isValid()) {
		//Appel de la doctrine et du EntityManager
            $em = $this->getDoctrine()->getEntityManager();
        //Stockage des objets dans une memoire temporaire
			$em->persist($entity);
        // Stockage des donnees dans la bdd
			$em->flush();

            return $this->redirect($this->generateUrl('article_show', array('id' => $entity->getId())));
            
        }

        return $this->render('SdzBlogBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('SdzBlogBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $editForm = $this->createForm(new ArticleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SdzBlogBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Article entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('SdzBlogBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $editForm   = $this->createForm(new ArticleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('article_edit', array('id' => $id)));
        }

        return $this->render('SdzBlogBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Article entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('SdzBlogBundle:Article')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Article entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('article'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
