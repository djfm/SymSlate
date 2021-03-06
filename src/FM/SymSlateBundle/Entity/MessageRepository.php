<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
	public function getTranslationInto($message_id, $language_code)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select("m, ct, t, l")
		   ->from('FMSymSlateBundle:Message', 'm')
		   ->innerJoin('m.current_translations', 'ct')
		   ->innerJoin('ct.translation', 't')
		   ->innerJoin('t.language', 'l')
		   ->where('l.code = :language_code')
		   ->andWhere('m.id = :message_id');

		$q  = $qb->getQuery();
		$q->setParameter('language_code', $language_code);
		$q->setParameter('message_id', $message_id);

		$result = $q->getResult();

		if(count($result) == 0)return false;
		else
		{
			$cts = $result[0]->getCurrentTranslations();
			return $cts[0]->getTranslation();
		}
	}

	public function getTranslationStringInto($message_id, $language_code)
	{
		$t = $this->getTranslationInto($message_id, $language_code);
		if($t == false)return false;
		else return $t->getText();
	}
}