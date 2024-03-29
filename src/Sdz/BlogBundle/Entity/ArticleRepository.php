<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    // Recuperer le total d article (pagination)
	public function getTotal()
    {
        $qb = $this->createQueryBuilder('a')
                   ->select('COUNT(a)');     // On sélectionne simplement COUNT(a)

        return (int) $qb->getQuery()
                         ->getSingleScalarResult(); // Utilisation de getSingleScalarResult pour avoir directement le résultat du COUNT
    }

    // Creation dune requette qui retour toute les donnees du tableau a (article)
	public function myFindAll()
	{
        return $this->createQueryBuilder('a')
        // Language query pour recuperer le resultat (getQueryOne() si la requette ne doit retourner qu un resultat COUNT(*)
				 ->getQuery()
                 ->getResult();
    }
	
	public function myFindOne($id)
    {
    // On passe par le QueryBuilder vide de l'EntityManager pour l'exemple
    return $this->createQueryBuilder('a')
                 ->where('a.id = :id')
                 // integration du parametre en transformant $id en id (:id)
				 ->setParameter('id', $id)
				 ->getQuery()
                 ->getResult();
    }
/* autre exemple de creation de requette avec EntityRepository
public function findByAuteurAndDate($auteur, $annee)
{
    // On utilise le QueryBuilder créé par le repository directement pour gagner du temps
    // Plus besoin de faire le select() ni le from() par la suite donc
    $qb = $this->createQueryBuilder('a');

    $qb->where('a.auteur = :auteur')
         ->setParameter('auteur', $auteur)
       ->andWhere('a.date < :annee')
         ->setParameter('annee', $annee)
       ->orderBy('a.date', 'DESC');

    return $qb->getQuery()
               ->getResult();
}
*/
// Methode agissant sur le QueryBuilder afin de creer une condition que l on pourra inclure dans nos requette
public function whereCurrentYear(Doctrine\ORM\QueryBuilder $qb)
{
    $qb->andWhere('a.date BETWEEN :debut AND :fin')
       ->setParameter('debut', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
       ->setParameter('fin',   new \Datetime(date('Y').'-12-31')); // Et le 31 décembre de cette année

    return $qb;
}
//exemple d utilisation de la condition crer dans le QueryBuilder
public function myFind()
{
    $qb = $this->createQueryBuilder('a');

    // On peut rajouter ce qu'on veut avant
    $qb->where('a.auteur = :auteur')
         ->setParameter('auteur', 'winzou');

    // On applique notre condition
    $qb = $this->whereCurrentYear($qb);

    // On peut rajouter ce qu'on veut après
    $qb->orderBy('a.date', 'DESC');
    
    return $qb->getQuery()
              ->getResult();
}
// Utilisation d une jointure (tjrs avec un parametre identique dans chaque entity)
// Possibilite de rajoute des condition supplementaire avec "WHITH"
// $qb->join('a.commentaires', 'c', 'WITH', 'YEAR(c.date) > 2011')
// Syntaxe sql :SELECT * FROM Article a JOIN Commentaire c ON c.article = a.id AND YEAR(c.date) > 2011

public function getArticleAvecCommentaires()
{
    $qb = $this->createQueryBuilder('a')
               ->join('a.commentaires', 'c')
               ->addSelect('c');

    return $qb->getQuery()
               ->getResult();
}

}