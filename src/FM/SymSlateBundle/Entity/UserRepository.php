<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRepository extends EntityRepository
{
	public function findAll($limit = null, $language_id = null)
	{
		if($language_id != null)
		{
			$lang = "AND t.language_id=".(int)$language_id;
		}
		else
		{
			$lang = "";
		}
		
		$query = $this->getEntityManager()->createQuery("SELECT u, count(t.id) as num
														 FROM FMSymSlateBundle:User u 
														 LEFT JOIN u.authored_translations t 
														 WITH t.translations_import_id IS NULL
														 WHERE u.email != 'pub@prestashop.com'
														 $lang
														 GROUP BY u.id
														 ORDER BY num DESC");
		
		if($limit != 0)$query->setMaxResults($limit);
		$result  = $query->getResult();
		
		$users   = array();

		foreach($result as $row)
		{
			$user = $row[0];
			$user->setTranslationsCount($row["num"]);
			$users[] = $user;
		}

		return $users;
	}
}