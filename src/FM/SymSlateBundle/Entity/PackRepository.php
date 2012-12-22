<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PackRepository extends EntityRepository
{
	public function getMessagesWithTranslations($pack_id, $language_id, $query_options = array(), $pagination_options = array())
	{
		$default_query_options = array(
			"empty" => "ONLY", 				// possible values are: ONLY, EXCEPT, anything else is treated as "take empty or non-empty"
			"category" => null,				// all categories
			"section" => null,				// all sections
			"subsection" => null,			// all subsections
			"source_language_id" => null	// language from which to translate
		);
		
		$default_pagination_options = array(
			"page_size" => 25,
			"page" => 1
		);
		
		$query_options = array_merge($default_query_options, $query_options);
		$pagination_options = array_merge($default_pagination_options, $pagination_options);
		
		if(isset($query_options['source_language_id']))
		{
			if($lang = $this->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findOneById($query_options['source_language_id']))
			{
				if($lang->getCode() == 'en')
				{
					$query_options['source_language_id'] = null;
				}
			}
			else $query_options['source_language_id'] = null;
		}
		
		$qb    = $this->getEntityManager()->createQueryBuilder();
		
		if(!$query_options['source_language_id'])
		{	
			$qb->select(array('m','c','ct','t'));
		}
		else
		{
			$qb->select(array('m','c','ct','t', 'sct', 'st'));
		}
		
		$qb->from('FMSymSlateBundle:Message','m')
		   ->innerJoin('m.classifications', 'c')
		   ->leftJoin ('c.current_translations','ct','WITH','ct.language_id = :language_id')
		   ->leftJoin ('ct.translation','t');
			
		if($query_options['source_language_id'])
		{
			$qb->leftJoin ('c.current_translations','sct','WITH','sct.language_id = :source_language_id')
		       ->leftJoin ('sct.translation','st');
		}	
			
		$qb->where('c.pack_id = :pack_id');
			
		if($query_options["empty"] == "ONLY")
		{
			$qb->andWhere('t.id IS NULL');
		}
		else if($query_options["empty"] == "EXCEPT")
		{
			$qb->andWhere('t.id IS NOT NULL');
		}
		
		if($query_options["category"] !== null)
		{
			$qb->andWhere("c.category = :category");
		}
		if($query_options["section"] !== null)
		{
			$qb->andWhere("c.section = :section");
		}
		if($query_options["subsection"] !== null)
		{
			$qb->andWhere("c.subsection = :subsection");
		}
		
		$query = $qb->getQuery();
								   
		$query->setParameter('pack_id', $pack_id);
		$query->setParameter('language_id', $language_id);
		
		if($query_options["category"] !== null)
		{
			$query->setParameter('category', $query_options["category"]);
		}
		if($query_options["section"] !== null)
		{
			$query->setParameter('section', $query_options["section"]);
		}
		if($query_options["subsection"] !== null)
		{
			$query->setParameter('subsection', $query_options["subsection"]);
		}
		if($query_options['source_language_id'])
		{
			$query->setParameter('source_language_id', $query_options["source_language_id"]);
		}
		
		$query->setMaxResults((int)$pagination_options["page_size"]);
		$query->setFirstResult(((int)$pagination_options["page"]-1)*((int)$pagination_options["page_size"]));
		
		
		$paginator = new Paginator($query, true);
		
		$messages = array();
			
		if($sid = $query_options['source_language_id'])
		{
			
		}
			
		foreach($paginator as $message)
		{
			$classifications = $message->getClassifications();
			if(count($classifications) !== 1)
			{
				echo "<p><span class='warning'>Message appears in several classifications: ".$message->getid().'</span></p>';
			}
			foreach($classifications as $classification)
			{
				$translation = '';
				$text        = '';
				$translation_id = null;
				
				if($num = count($ct = $classification->getCurrentTranslations()) > 1)
				{
					$too_many_translations = false; //be optimistic :)
					if($num == 2)
					{	
						if(($ct[0]->getLanguageId() == $language_id) and ($ct[1]->getLanguageId() == $query_options['source_language_id']))
						{
							$text           = $ct[1]->getTranslation()->getText();
							$translation    = $ct[0]->getTranslation()->getText();
							$translation_id = $ct[0]->getTranslation()->getId();
						}
						else if(($ct[0]->getLanguageId() == $language_id) and ($ct[1]->getLanguageId() == $query_options['source_language_id']))
						{
							$text           = $ct[0]->getTranslation()->getText();
							$translation    = $ct[1]->getTranslation()->getText();
							$translation_id = $ct[1]->getTranslation()->getId();
						}
						else $too_many_translations = true;
						
						if(!$too_many_translations)
						{
							if(count(trim($text)) == 0)
							{
								$text = $message->getText();
							}
						}
					}
					if($num > 2 or $too_many_translations)
					{
						echo "<p><span class='error'>Message has multiple current translations (this is a DB bug): ".$message->getid().'</span></p>';
						echo "<ul>";
						foreach($classification->getCurrentTranslations() as $ct)
						{
							echo "<li>".$ct->getTranslation()->getText()."</li>";
						}
						echo "</ul>";
					}
				}
				else if(count($classification->getCurrentTranslations()) == 1)
				{
					$cts  = $classification->getCurrentTranslations();
					$t = $cts[0]->getTranslation();
					if($t->getLanguageId() == $language_id)
					{
						$text = $message->getText();
						$translation = $t->getText();
						$translation_id = $t->getId();
					}
					else
					{
						$text = $t->getText();
					}
				}
				else
				{
						$text = $message->getText();
				}
				
				$messages[$classification->getCategory()][$classification->getSection()][$classification->getSubSection()][] = array(
					"text" => $text,
					"translation" => $translation,
					"translation_id" => $translation_id,
					"type" => $message->getType(),
					"classification_id" => $classification->getId(),
					"message_id" => $message->getId()
				);
				
				//echo "<p>".$message->getText()."</p>";
			}
		}
		
		$pagination = array(
			'total_count' => count($paginator),
			'page' => $pagination_options['page'],
			'page_size' => $pagination_options['page_size'],
			'page_count' => round((int)count($paginator)/(int)$pagination_options['page_size'])
		);
		
		if((int)$pagination_options['page'] > 1)
		{
			$pagination['previous_page'] = (int)$pagination_options['page'] - 1;
		}
		if((int)$pagination_options['page'] < $pagination['page_count'])
		{
			$pagination['next_page'] = (int)$pagination_options['page'] + 1;
		}
		
		return array('messages' => $messages, 
					 'pagination' => $pagination
					);
		
	}

	public function getStoragesWithTranslations($pack_id, $language_id)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select(array('s','m','c','ct','t'))
		   ->from('FMSymSlateBundle:Storage', 's')
		   ->innerJoin('s.message', 'm')
		   ->innerJoin('m.classifications', 'c')
		   ->innerJoin('c.current_translations', 'ct')
		   ->innerJoin('ct.translation', 't')
		   ->where('s.pack_id = :pack_id')
		   ->andWhere('c.pack_id = :pack_id')
		   ->andWhere('ct.language_id = :language_id');
		   
		$query = $qb->getQuery();
		$query->setParameter('pack_id', $pack_id);
		$query->setParameter('language_id', $language_id);
		
		return $query->getResult();
		
	}

	public function computeStatistics($pack_id, $language_id)
	{
		$query = $this->getEntityManager()->createQuery(
		"SELECT c.category, count(c.id) as total, count(ct.id) as translated FROM FMSymSlateBundle:Classification c LEFT JOIN c.current_translations ct
		 WITH ct.language_id = :language_id
		 WHERE c.pack_id = :pack_id
		 GROUP BY c.category
		"		
		);
		$query->setParameter(':language_id',$language_id);
		$query->setParameter(':pack_id',$pack_id);
		$results = $query->getResult();
		
		//print_r($results);
		
		$stats = array(null => array('total' => 0, 'translated' => 0, 'percent' => 0));
		$cats  = array(null);
		
		foreach($results as $row)
		{
			$cats[] = $row['category'];
			$stats[$row['category']] = array('total' => (int)$row['total'], 'translated' => (int)$row['translated'], 'percent' => 100 * (int)$row['translated'] / (int)$row['total'] );
			$stats[null]['total'] += (int)$row['total'];
			$stats[null]['translated'] += (int)$row['translated'];
		}
		
		$stats[null]['percent'] = 100 * $stats[null]['translated'] / $stats[null]['total'];
		
		return array("categories" => $cats, "statistics" => $stats);
	}
	
	public function getPackNames()
	{
		$packNames = array();
		foreach($this->findAll() as $pack)
		{
			$packNames[$pack->getId()] = $pack->getFullName();
		}
		return $packNames;
	}

}
