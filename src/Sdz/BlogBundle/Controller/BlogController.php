<?php

// src/Sdz/BlogBundle/Controller/BlogController.php

namespace Sdz\BlogBundle\Controller;

// Permet d heriter de la classe ContainerAware qui possede l attribut (class) container qui contient les services
// L appel des service : Donc dans un contrôleur, $this->get('...') est strictement équivalent à $this->container->get('...').
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use reponse n est pas obligation si on utilise seulement l affichage avec la methode return $this->render ()
use Symfony\Component\HttpFoundation\Response;
// Objet contenant les donnees de la bdd. Permet aussi le traitement directe de l existence d une variable transmise par Get afi d afficher un message d erreur 
use Sdz\BlogBundle\Entity\Article;
//Utilisation du fichier de contruction ddu formulaire avec l objet article
use Sdz\BlogBundle\Form\ArticleType;
//Externalisation du tratement du formulaire
use Sdz\BlogBundle\Form\ArticleHandler;
// Determiner les roles par annotations en utilisant le bundle JMSSecurityExtraBundle:
use JMS\SecurityExtraBundle\Annotation\Secure;


// Objet Blog Bundle extence dans le controller
class BlogController extends Controller
{
	public function indexAction($page)
    {
        // On récupère le repository
        $repository = $this->getDoctrine()
                           ->getEntityManager()
                           ->getRepository('SdzBlogBundle:Article');

        // On récupère le nombre total d'articles
        $nb_articles = $repository->getTotal();

        // On définit le nombre d'articles par page
        // (pour l'instant en dur dans le contrôleur, mais par la suite on le transformera en paramètre du bundle)
        $nb_articles_page = 2;

        // On calcule le nombre total de pages. ceil () arrondie au chiffre superieur
        $nb_pages = ceil($nb_articles/$nb_articles_page);

        // On va récupérer les articles à partir du N-ième article :
        $offset = ($page-1) * $nb_articles_page;

        // Ici on a changé la condition pour déclencher une erreur 404
        // lorsque la page est inférieur à 1 ou supérieur au nombre max.
        if( $page < 1 OR $page > $nb_pages )
        {
            throw $this->createNotFoundException('Page inexistante (page = '.$page.')');
        }

        // On récupère les articles qu'il faut grâce à findBy() :
        $articles = $repository->findBy(
            array(),                 // Pas de critère
            array('date' => 'desc'), // On tri par date décroissante
            $nb_articles_page,       // On sélectionne $nb_articles_page articles
            $offset                  // A partir du $offset ième
        );

        return $this->render('SdzBlogBundle:Blog:index.html.twig', array(
            'articles' => $articles,
            'page'     => $page,    // On transmet à la vue la page courante,
            'nb_pages' => $nb_pages // Et le nombre total de pages.
        ));
    }

    public function voirAction(Article $article)
    {

        return $this->render('SdzBlogBundle:Blog:voir.html.twig', array(
            'article' => $article
        ));
    }
    
	/**
     * @Secure(roles="ROLE_AUTEUR")
     */
	public function ajouterAction()
    {
        
		$article = new Article;

        // On crée le formulaire
        $form = $this->createForm(new ArticleType, $article);

        // On crée le gestionnaire pour ce formulaire, avec les outils dont il a besoin
        $formHandler = new ArticleHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        // On exécute le traitement du formulaire. S'il retourne true, alors le formulaire a bien été traité
        if( $formHandler->process() )
        {
            return $this->redirect( $this->generateUrl('sdzblog_voir', array('id' => $article->getId())) );
        }

        // Et s'il retourne false alors la requête n'était pas en POST ou le formulaire non valide.
        // On réaffiche donc le formulaire.
        return $this->render('SdzBlogBundle:Blog:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $article = $em->getRepository('Sdz\BlogBundle\Entity\Article')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new ArticleType, $article);
        $formHandler = new ArticleHandler($form, $this->get('request'), $em);

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('sdzblog_voir', array('id' => $article->getId())) );
        }

        return $this->render('SdzBlogBundle:Blog:modifier.html.twig', array(
            'form' => $form->createView(),
			'id'=>$id,
        ));
    }

    public function supprimerAction($id)
    {
        
         $em = $this->getDoctrine()->getEntityManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $article = $em->getRepository('Sdz\BlogBundle\Entity\Article')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
            $em->remove($article);
            $em->flush();
			
        return $this->redirect($this->generateUrl('sdzblog_accueil'));
    }

	public function testAction()
    {
        $article = new Article;
        
        $article->setDate(new \Datetime());  // Champ « date » O.K.
        $article->setTitre('abc');           // Champ « titre » incorrect : moins de 10 caractères.
        //$article->setContenu('blabla');    // Champ « contenu » incorrect : on ne le définit pas.
        $article->setAuteur('A');            // Champ « auteur » incorrect : moins de 2 caractères.
        
        // On récupère le service validator.
        $validator = $this->get('validator');
        
        // On déclenche la validation.
        $liste_erreurs = $validator->validate($article);

        // Si le tableau n'est pas vide, on affiche les erreurs.
        if(count($liste_erreurs) > 0)
        {
            return new Response(print_r($liste_erreurs, true));
        }
        else
        {
            return new Response("L'article est valide !");
        }
    }

	
/*
	public function indexAction($page)
    {
        // Ici le contenu de la méthode.
        return new Response("Page accueil n ' ".$page.".");
    }
	
	// retour d une page par selection  template twig en utilisant la methode render (). 
	public function bybyAction()
    {
       return $this->render('SdzBlogBundle:Blog:byby.html.twig', array('nom' => 'winzou')); 
    }

    // On récupère tous les paramètres en argument (variable) de la méthode (funtion) et utilisation de la methode Response () pour l affichage de la page.
    public function voirSlugAction($slug, $annee, $format)
    {
        // Ici le contenu de la méthode.
        return new Response("On pourrait afficher l'article correspondant au slug '".$slug."', créé en ".$annee." et au format ".$format.".");
    }
	
/*	
	// La route fait appel à SdzBlogBundle:Blog:voir, on doit donc définir la méthode "voirAction".
    // On donne à cette méthode l'argument $id, pour correspondre au paramètre {id} de la route.
    public function voirAction($id)
    {
        // $id vaut 5 si l'on a appelé l'URL /blog/article/5.
        
        // Ici, on récupèrera depuis la base de données l'article correspondant à l'id $id.
        // Puis on passera l'article à la vue pour qu'elle puisse l'afficher.

        return new Response("Affichage de l'article d'id : ".$id.".");
    }
	
	// $id vaut 5 si l'on a appelé l'URL /blog/article/5.
	// afficher une page avec la methode render () en recuperant le parametre id 
	public function voirAction($id)
    {
        // On utilise la méthode render()
        return $this->render('SdzBlogBundle:Blog:voir.html.twig', array('id' => $id));
    }
*/
/*
	// url: /blog/article/5?tag=vacances
	public function voirAction($id)
    {
        // On récupère la requête en recuperant l object REQUEST avec la methode get('request').
        $request = $this->get('request');

        // On récupère notre paramètre tag avec la methode query->get('nom du parametre').
		// Recuperation paramettre dans Get (url) : Utilisation de la methode query->get()
		// Recuperation parametre dans Post (form) : Utilisation de la methode request->get()
		// Recuperation parametre dans Cookie : Utilisation de la methode cookies->get()
        $tag = $request->query->get('tag');
		
		// Affichage de la page avec la methode render ('chemin de la page',array('')
        return $this->render('SdzBlogBundle:Blog:voir.html.twig', array(
            'id'  => $id,
            'tag' => $tag
        ));
    }
*/
/*	
	// Recuperer la methode de la requette http 
	(liste: http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html)
	// Methode POST
	if( $request->getMethod() == 'POST' )
{
    // Un formulaire a été envoyé, on peut le traiter ici.
}

	// Methode AJAX
	if( $request->isXmlHttpRequest() )
{
    // C'est une requête Ajax, retournons du JSON, par exemple.
}
*/
/*
	// Effectuer une redirection avec la methode redirect ($this->page de redirection)
	// url : /blog/article/5
    public function voirAction($id)
    {
        // On utilise la méthode « generateUrl() » pour obtenir l'URL de la liste des articles à la page 2, par exemple. Vous serez redirige vers la page accueil
        return $this->redirect( $this->generateUrl('sdzblog_accueil', array('page' => 2)) );
    }
/*	// Changer le content-type de la reponse (exemple : si nous sommes en html et on renvoi des infos en xml)
	// url : blog/article/5
	public function voirAction($id)
    {
        // Créons nous-mêmes la réponse en JSON, grâce à la fonction json_encode().
        $response = new Response(json_encode(array('id' => $id)));

        // Ici, nous définissons le « Content-type » pour dire que l'on renvoie du JSON et non du HTML.
        $response->headers->set('Content-Type', 'application/json');

        return $response;

        // Nous n'avons pas utilisé notre template ici, car il n'y en a pas vraiment besoin.
    }
*/

/*	// url : blog/article/5
	// Utilisation des service avec la methode $this->get(service)
	// Exemple utilisation du service envoi mail avec le servie :mailer
	public function voirAction($id)
    {
        // Récupération du service.
        $mailer = $this->get('mailer');

        // Création de l'e-mail : le service mailer utilise SwiftMailer, donc nous créons une instance de Swift_Message.
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello zéro !')
            ->setFrom('agenda@virusalsa.com')
            ->setTo('jlregis@hotmail.fr')
            ->setBody('Coucou, voici un email que vous venez de recevoir !');

        // Retour au service mailer, nous utilisons sa méthode « send() » pour envoyer notre $message.
        $mailer->send($message);

        // N'oublions pas de retourner une réponse, par exemple, une page qui afficherait « L'email a bien été envoyé ».
        return new Response('Email bien envoyé');
    }
*/
/*	
	// Utilisation du service SESSION 
	public function voirAction($id)
    {
        // Récupération du service
        $session = $this->get('session');
    
        // On récupère le contenu de la variable user_id
        $user_id = $session->get('user_id');

        // On définit une nouvelle valeur pour cette variable user_id
        $session->set('user_id', 91);

        // On n'oublie pas de renvoyer une réponse
        return new Response('Désolé je suis une page de test, je n\'ai rien à dire'.$id.$user_id.' teste session');
	}
  */
}