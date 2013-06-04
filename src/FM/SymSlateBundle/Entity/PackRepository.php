<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

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
			"empty" 				=> "ONLY", 				// possible values are: ONLY, EXCEPT, anything else is treated as "take empty or non-empty"
			"category" 				=> null,				// all categories
			"section" 				=> null,				// all sections
			"subsection" 			=> null,				// all subsections
			"source_language_id" 	=> null,				// language from which to translate
			"message_like" 			=> null,
			"translation_like" 		=> null,
			"author_id" 			=> null,
			"show_context" 			=> true,
			"has_error" 			=> null,
			"has_warning" 			=> null,
			"not_in"      			=> null
		);
		
		$default_pagination_options = array(
			"page_size" => 25,
			"page" => 1,
			"disable_pagination" => false
		);
		
		$query_options = array_merge($default_query_options, $query_options);
		$pagination_options = array_merge($default_pagination_options, $pagination_options);
		
		if(null !== $query_options['source_language_id'])
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
			$qb->select(array('DISTINCT c','m','ct','t'));
		}
		else
		{
			$qb->select(array('DISTINCT c','m','ct','t', 'sct', 'st'));
		}
		
		$qb->from('FMSymSlateBundle:Classification','c')
		   ->innerJoin('c.message', 'm')
		   ->leftJoin ('m.current_translations','ct','WITH','ct.language_id = :language_id')
		   ->leftJoin ('ct.translation','t');
			
		if($query_options['source_language_id'])
		{
			$qb->leftJoin ('m.current_translations','sct','WITH','sct.language_id = :source_language_id');
			$qb->leftJoin ('sct.translation','st');		       
		}	
		
		if(null !== $query_options['not_in'])
		{
			$qb->leftJoin('m.classifications','mc','WITH','mc.pack_id = :not_in');
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
		if($query_options["author_id"] !== null)
		{
			$qb->andWhere("t.created_by = :created_by");
			$qb->andWhere("t.translations_import_id IS NULL");
		}
		if($query_options["has_error"] !== null)
		{
			$qb->andWhere('t.has_error = :has_error');
		}
		if($query_options["has_warning"] !== null)
		{
			$qb->andWhere('t.has_warning = :has_warning');
		}
		if(null !== $query_options["message_like"])
		{
			if(null === $query_options['source_language_id'])
			{
				$qb->andWhere("m.text LIKE :message_like");
			}
			else
			{
				$qb->andWhere("st.text LIKE :message_like");
			}
		}
		if($query_options["translation_like"] !== null)
		{
			$qb->andWhere("t.text LIKE :translation_like");
		}

		if(isset($query_options['positions']))
		{
			if((null === $query_options['positions']) or (0 ===  count($query_options['positions'])))
			{
				$qb->andWhere("c.position IS NULL");
			}
			else
			{
				$qb->andWhere("c.position IN (" . implode(", ",array_map('intval', $query_options['positions'])) . ")");
			}
			
		}
		if(isset($query_options['translation_different_from_source']) and $query_options['translation_different_from_source'] == 1)
		{
			$qb->andWhere('m.text != t.text');
		}

		if(null !== $query_options['not_in'])
		{
			$qb->andWhere('mc.id IS NULL');
		}
		


		$qb->orderBy('c.position', 'ASC');
		
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
		if(null !== $query_options["message_like"])
		{
			$query->setParameter('message_like', $query_options["message_like"]);
		}
		if($query_options["translation_like"] !== null)
		{
			$query->setParameter('translation_like', $query_options["translation_like"]);
		}
		if($query_options["author_id"] !== null)
		{
			$query->setParameter('created_by', $query_options["author_id"]);
		}
		if($query_options["has_error"] !== null)
		{
			$query->setParameter('has_error', $query_options["has_error"]);
		}
		if($query_options["has_warning"] !== null)
		{
			$query->setParameter('has_warning', $query_options["has_warning"]);
		}
		if(null !== $query_options['not_in'])
		{
			$query->setParameter('not_in', $query_options["not_in"]);
		}
		
		
		if(!$pagination_options["disable_pagination"])
		{
			$query->setMaxResults((int)$pagination_options["page_size"]);
			$query->setFirstResult(((int)$pagination_options["page"]-1)*((int)$pagination_options["page_size"]));
		}
		
		$paginator = new Paginator($query, true);

		$messages = array();
		$context_messages = null;
		//get context if we are not already in a context query and it is needed and not explicitely refused
		if(!(isset($query_options["is_context"]) and $query_options["is_context"]) 
			and (   (null !== $query_options["message_like"]) 
				 or (null !== $query_options["translation_like"]) 
				 or ('ONLY'   === $query_options["empty"]) 
				 or ('EXCEPT' === $query_options["empty"])
				 or (null !== $query_options["author_id"])
				 or (null !== $query_options["has_error"])
				 or (null !== $query_options["has_warning"])
				)
			and $query_options['show_context']
			)
		{
			//get the positions around the selected messages
			$positions = array();
			$already_got = array();
			$context   = 2;
			foreach($paginator as $classification)
			{
				for($p = $classification->getPosition() - $context; $p <= $classification->getPosition() + $context; $p += 1)
				{
					if($p >= 0)
					{
						if($p != $classification->getPosition())
						{
							$positions[] = $p;
						}
						else
						{
							$already_got[] = $p;
						}
					}
				}
			}
			//print_r($positions);
			$positions = array_diff(array_unique($positions), $already_got);

			$qo = array(
				"category" => $query_options["category"],
				"section" => $query_options["section"],
				"source_language_id" => $query_options['source_language_id'],
				"empty" => null, //empty or not
				"positions" => $positions,
				"message_like" => null,
				"translation_like" => null,
				"is_context" => true
			);

			$po = array("disable_pagination" => true);

			$context_messages = $this->getMessagesWithTranslations($pack_id, $language_id, $qo, $po);
			$messages = $context_messages['messages'];
		}
		
		
			
		if($sid = $query_options['source_language_id'])
		{
			
		}
			
		foreach($paginator as $classification)
		{
			
			$translation = '';
			$translation_error_message = '';
			$translation_warning_message = '';
			$text        = null;
			$translation_id = null;
			
			$message = $classification->getMessage();

			$cts     = $message->getCurrentTranslations();

			
			/**
			 * There are 0 - 2 $cts depending on
			 *  -whether we requested a source language translation
			 *  -whether the source and dest language translations were found
			 */

			foreach($cts as $ct)
			{
				//get the translation
				if($ct->getLanguageId() == $language_id)
				{
					$translation    = $ct->getTranslation()->getText();
					$translation_id = $ct->getTranslation()->getId();
					$translation_error_message   = $ct->getTranslation()->getErrorMessage();
					$translation_warning_message = $ct->getTranslation()->getWarningMessage();
				}
				//get the message translation if required
				if(isset($query_options['source_language_id']) and $ct->getLanguageId() == $query_options['source_language_id'])
				{
					$text = $ct->getTranslation()->getText();
				}
			}

			//set the default message text if message translation in source language was not found or not requested
			if(null === $text)$text = $message->getText();
			
			$messages[$classification->getCategory()][$classification->getSection()][$classification->getSubSection()][] = array(
				"text" => $text,
				"translation" => $translation,
				"translation_id" => $translation_id,
				"type" => $message->getType(),
				"classification_id" => $classification->getId(),
				"message_id" => $message->getId(),
				"is_context" => isset($query_options['is_context']) and $query_options['is_context'],
				"position" => $classification->getPosition(),
				"error_message" => $translation_error_message,
				"warning_message" => $translation_warning_message
			);
				
				//echo "<p>".$message->getText()."</p>";
		}
		
		ksort($messages);
		foreach($messages as $category => &$ss)
		{
			ksort($ss);
			foreach ($ss as $section => &$ssms) 
			{
				ksort($ssms);
				foreach($ssms as $subsection => &$ms)
				{
					usort($ms, function($a, $b){
						return $a['position'] - $b['position'];
					});
				}
			}
		}

		$pagination = array(
			'total_count' => count($paginator),
			'page' => $pagination_options['page'],
			'page_size' => $pagination_options['page_size'],
			'page_count' => ceil((int)count($paginator)/(int)$pagination_options['page_size'])
		);
		
		return array('messages' => $messages, 
					 'pagination' => $pagination
					);
		
	}

	public function getStoragesWithTranslations($pack_id, $language_id)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select(array('s','m','ct','t'))
		   ->from('FMSymSlateBundle:Storage', 's')
		   ->innerJoin('s.message', 'm')
		   ->innerJoin('m.current_translations', 'ct')
		   ->innerJoin('ct.translation', 't')
		   ->where('s.pack_id = :pack_id')
		   ->andWhere('ct.language_id = :language_id')
		   ->andWhere('t.has_error = 0');
		   
		$query = $qb->getQuery();

		$query->setFetchMode('FMSymSlateBundle:Storage', 'message', 'EAGER');
		$query->setFetchMode('FMSymSlateBundle:Message', 'current_translations', 'EAGER');
		$query->setFetchMode('FMSymSlateBundle:CurrentTranslation', 'translation', 'EAGER');

		$query->setParameter('pack_id', $pack_id);
		$query->setParameter('language_id', $language_id);
		
		return $query->getResult();
		
	}

	public function computeStatistics($pack_id, $language_id, $cheat=true)
	{	
		$stats = array(null => array('total' => 0, 'translated' => 0, 'percent' => 0));
		$cats  = array(null);
		
		$language = $this->getEntityManager()->getRepository('FMSymSlateBundle:Language')->find($language_id);
		
		//English is at 100% by definition for now
		if($cheat and $language->getCode() == 'en')
		{
			$query = $this->getEntityManager()->createQuery(
				"SELECT c.category, count(c.id) as n FROM FMSymSlateBundle:Classification c
				 WHERE c.pack_id = :pack_id
				 GROUP BY c.category
				"		
			);
			$query->setParameter(':pack_id',$pack_id);
			$results = $query->getResult();
			foreach($results as $row)
			{
				$cats[] = $row['category'];
				$stats[$row['category']] = array('total' => (int)$row['n'], 'translated' => (int)$row['n'], 'percent' => 100);
				$stats[null]['total'] += (int)$row['n'];
				$stats[null]['translated'] += (int)$row['n'];
			}

			$stats[null]['percent'] = 100;
		}
		else
		{
			$query = $this->getEntityManager()->createQuery(
			"SELECT c.category, count(c.id) as total, count(ct.id) as translated 
			 FROM FMSymSlateBundle:Classification c 
			 INNER JOIN c.pack p
			 LEFT JOIN p.ignored_sections igs WITH igs.language_id = :language_id AND igs.category = c.category AND igs.section = c.section
			 LEFT JOIN c.message m
			 LEFT JOIN m.current_translations ct
			 WITH ct.language_id = :language_id
			 WHERE c.pack_id = :pack_id AND igs.id IS NULL
			 GROUP BY c.category
			"		
			);
			$query->setParameter(':language_id',$language_id);
			$query->setParameter(':pack_id',$pack_id);
			$results = $query->getResult();
			
			foreach($results as $row)
			{
				$cats[] = $row['category'];
				$stats[$row['category']] = array('total' => (int)$row['total'], 'translated' => (int)$row['translated'], 'percent' => 100 * (int)$row['translated'] / (int)$row['total'] );
				$stats[null]['total'] += (int)$row['total'];
				$stats[null]['translated'] += (int)$row['translated'];
			}
		
			$stats[null]['percent'] = $stats[null]['total'] > 0 ? 100 * $stats[null]['translated'] / $stats[null]['total'] : 0;
		}

		return array("categories" => $cats, "statistics" => $stats);
	}

	public function computeDetailedStatistics($pack_id, $language_id)
	{	
		$query = $this->getEntityManager()->createQuery(
		"SELECT c.category, c.section, m.text as message, t.text as translation
		 FROM FMSymSlateBundle:Classification c
		 LEFT JOIN c.message m
		 LEFT JOIN m.current_translations ct WITH ct.language_id = :language_id
		 LEFT JOIN ct.translation t
		 WHERE c.pack_id = :pack_id
		"		
		);
		$query->setParameter(':language_id',$language_id);
		$query->setParameter(':pack_id',$pack_id);
		$results = $query->getResult();

		$stats = array(null => 
					array(null => array(
							'total_strings' => 0, 
							'translated_strings' => 0,
							'total_words' => 0, 
							'translated_words' => 0,
							'words_in_translation' => 0,
							'percent' => 0
						)
					)
				);

		$cats  = array();

		foreach($results as $row)
		{
			if(!isset($stats[$row['category']]))
			{
				$cats[] = $row['category'];

				$stats[$row['category']] = array();
				$stats[$row['category']][null] = array(
					'total_strings' => 0, 
					'translated_strings' => 0,
					'total_words' => 0, 
					'translated_words' => 0,
					'words_in_translation' => 0,
					'percent' => 0
				);
			}

			if(!isset($stats[$row['category']][$row['section']]))
			{
				$stats[$row['category']][$row['section']] = array(
					'total_strings' => 0, 
					'translated_strings' => 0,
					'total_words' => 0, 
					'translated_words' => 0,
					'words_in_translation' => 0,
					'percent' => 0
				);
			}


			$wc  = str_word_count(strip_tags($row['message']));
			$ts  = $row['translation'] == null ? 0 : 1;
			$tw  = $row['translation'] == null ? 0 : $wc;
			$wit = $row['translation'] == null ? 0 : str_word_count(strip_tags($row['translation']));

			$stats[$row['category']][$row['section']]['total_strings'] 			+= 1;
			$stats[$row['category']][$row['section']]['translated_strings'] 	+= $ts;
			$stats[$row['category']][$row['section']]['total_words'] 			+= $wc;
			$stats[$row['category']][$row['section']]['translated_words'] 		+= $tw;
			$stats[$row['category']][$row['section']]['words_in_translation'] 	+= $wit;

			$stats[null][null]['total_strings'] 								+= 1;
			$stats[null][null]['translated_strings'] 							+= $ts;
			$stats[null][null]['total_words'] 									+= $wc;
			$stats[null][null]['translated_words'] 								+= $tw;
			$stats[null][null]['words_in_translation'] 							+= $wit;

			$stats[$row['category']][null]['total_strings'] 					+= 1;
			$stats[$row['category']][null]['translated_strings'] 				+= $ts;
			$stats[$row['category']][null]['total_words'] 						+= $wc;
			$stats[$row['category']][null]['translated_words'] 					+= $tw;
			$stats[$row['category']][null]['words_in_translation'] 				+= $wit;
		}

		foreach($stats as $cat => $ss)
		{
			foreach($ss as $s => $unused)
			{
				$stats[$cat][$s]['percent']         = 100 * $stats[$cat][$s]['translated_strings'] / $stats[$cat][$s]['total_strings'];
				$stats[$cat][$s]['remaining_words'] = $stats[$cat][$s]['total_words'] - $stats[$cat][$s]['translated_words'];
			}
		}

		foreach($stats as $cat => &$sections)
		{
			uasort($sections, function($s, $t){
				return $s['percent'] < $t['percent'] ? -1 : ($s['percent'] == $t['percent'] ? 0 : 1);
			});
		}

		return array("categories" => $cats, "statistics" => $stats);
	}

	public function computeAllStatistics($pack_id, $force_refresh=false, $cheat=true, $refresh_interval=1440)
	{
		$pack = $this->find($pack_id);

		if($cheat and !$force_refresh and null !== $pack->getStatisticsUpdated())
		{
			$now   = new \DateTime("now");
			$delta = $now->diff($pack->getStatisticsUpdated());
			$minutes = ($delta->days * 24 + $delta->h) * 60 + $delta->i;

			if($minutes < $refresh_interval)
			{
				$result = json_decode($pack->getStatistics(),true);
				//print_r($result);
				return $result;
			}
		}

		$stats = array();
		$cats  = null;
		foreach($this->getEntityManager()->getRepository('FMSymSlateBundle:Language')->findAll() as $language)
		{
			$st   = $this->computeStatistics($pack_id, $language->getId(), $cheat);
			$cats = $st['categories'];
			$stats[$language->getAName()] = array('code' => $language->getCode(), 'statistics' => $st['statistics']);
		}

		uasort($stats, function($s, $t){
			if($s['statistics'][null]['percent'] >= $t['statistics'][null]['percent'])return -1;
			else if($s['statistics'][null]['percent'] < $t['statistics'][null]['percent'])return 1;
			else return 0;
		});

		$result = array('categories' => $cats, 'statistics' => $stats);

		if($cheat)
		{
			$pack = $this->find($pack_id);
			$pack->setStatistics(json_encode($result));
			$pack->setStatisticsUpdated(new \DateTime("now"));

			$this->getEntityManager()->persist($pack);
			$this->getEntityManager()->flush();
		}
		return $result;
	}
	
	public function setCurrent($pack_id)
	{
		$this->getEntityManager()->createQuery("UPDATE FMSymSlateBundle:Pack p SET p.is_current = CASE WHEN p.id = :pack_id THEN true ELSE false END")
								 ->setParameter('pack_id',$pack_id)
								 ->getResult();
	    
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

	public function getCategoriesAndSections($pack_id)
	{
		$q = $this->getEntityManager()->createQuery("SELECT DISTINCT c.category, c.section, m.text as message FROM FMSymSlateBundle:Classification c INNER JOIN c.message m WHERE c.pack_id=:pack_id");
		$q->setParameter('pack_id', $pack_id);

		$res = array();
		foreach($q->getResult() as $row)
		{
			if(!isset($res[$row['category']]))
			{
				$res[$row['category']] = array();
			}
			if(!isset($res[$row['category']][$row['section']]))
			{
				$res[$row['category']][$row['section']] = array();
			}
			$res[$row['category']][$row['section']][] = $row['message'];
		}
		return $res;
	}

}
