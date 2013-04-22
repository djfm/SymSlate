<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * HistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoryRepository extends EntityRepository
{
	public function getTranslationHistory($message_id, $language_code)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select("h, t")
		   ->from('FMSymSlateBundle:History', 'h')
		   ->innerJoin('h.translation', 't')
		   ->innerJoin('h.language', 'l')
		   ->where('l.code = :language_code')
		   ->andWhere('h.message_id = :message_id')
		   ->orderBy('h.id', 'DESC');

		$q  = $qb->getQuery();
		$q->setParameter('language_code', $language_code);
		$q->setParameter('message_id'   , $message_id);

		return $q->getResult();
	}
}