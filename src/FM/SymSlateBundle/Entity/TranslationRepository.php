<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TranslationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TranslationRepository extends EntityRepository
{
	public function translateFromEnglishInto($message, $language_code)
	{
		$em = $this->getEntityManager();
		$q  = $em->createQuery("SELECT t.text FROM FMSymSlateBundle:CurrentTranslation ct
								INNER JOIN ct.translation t
								INNER JOIN ct.message m
								INNER JOIN ct.language l
						  WHERE m.text = :message AND l.code=:language_code
						  ORDER BY t.id DESC 
		");

		$q->setParameter('message', $message);
		$q->setParameter('language_code', $language_code);
		$q->setMaxResults(1);

		$res = $q->getResult();

		return empty($res) ? null : $res[0]['text'];
	}

	/**
	* WARNING: $source_language_code is not used yet, only English!
	*/

	public function getSuggestions($source_language_code, $language_code, $message)
	{
		$tokenize = function($str)
		{
			$tokens = array();
			foreach(preg_split('/(\s|\W)+/', $str) as $tok)
			{
				if(mb_strlen($tok, 'utf8') >= 4)
				{
					$tokens[] = strtolower($tok);
				}
			}

			return $tokens;
		};

		$t_message = $tokenize($message);
		if(count($t_message) <= 1)
		{
			return array();
		}


		$em = $this->getEntityManager();

		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select(array('DISTINCT m.text as message', 't.text as translation'))
			->from('FMSymSlateBundle:CurrentTranslation', 'ct')
			->innerJoin('ct.message', 'm')
			->innerJoin('ct.translation', 't')
			->innerJoin('ct.language', 'l')
			->where('l.code = :language_code')
			->andWhere('m.type = \'STRING\'');

		$q = $qb->getQuery();

		$q->setParameter('language_code', $language_code);

		$dictionary = $q->getResult();

		$message = strtolower(trim($message));

		$candidates = array();

		foreach($dictionary as $row)
		{
			$ct_message = $tokenize($row['message']);
			if(!empty($ct_message))
			{
				$inter = array_intersect(array_unique($ct_message), array_unique($t_message));
				$score = ((count($inter) / count($ct_message)) + (count($inter) / count($t_message))) / 2;

				if($score > 2)
				{
					die(print_r(array(
						'ct_message' => $ct_message,
						't_message' => $t_message,
						'inter' => $inter
					),1));
				}

				if($score >= 0.5)
				{
					$candidates[] = array("score" => $score, 'match' => $row);
				}
			}
		}

		usort($candidates, function($a, $b){
			return ($a['score'] < $b['score'] ? 1 : -1);
		});

		return array_map(function($row){return $row['match'];}, array_slice($candidates, 0, 10));
	}

}
