<?php

namespace FM\SymSlateBundle\Service;

class AutocompleteService extends \FM\SymSlateBundle\Worker\Worker
{

	public function normalize($str)
	{
		$nz = strtolower($str);
		$nz = preg_replace('/[:\.;!-]+/',' ',$str);
		$nz = preg_replace('/\s+/',' ',$str);
		return $nz;
	}

	public function run($args)
	{
		$this->setStatus("Started!");

		$language_ids = isset($args['language_ids']) ? $args['language_ids'] : null;



		if( null === $language_ids or count($language_ids) == 0)$language_ids = array_map(function($language){
			return $language->getId();
		},
		$this->em->getRepository('FMSymSlateBundle:Language')->findAll());
		else $language_ids = array_map('intval', $language_ids);
		
		$expected_steps = $this->em->createQuery("SELECT count(m.id) 
												  FROM FMSymSlateBundle:Message m
												  LEFT JOIN m.current_translations ct
												  WITH ct.language_id IN (" . implode(", ", $language_ids) . ")
												  WHERE ct.id IS NULL
												")
						   ->getSingleScalarResult();
		$this->setExpectedSteps($expected_steps);

		foreach($language_ids as $language_id)
		{
			//todo : take current translations in priority			
			$query = $this->em->createQuery("
				SELECT t FROM FMSymSlateBundle:Translation t WHERE 
				t.language_id = :language_id
				AND
				t.id = (SELECT max(u.id) FROM FMSymSlateBundle:Translation u
					WHERE u.language_id = :language_id AND u.mkey=t.mkey)
				ORDER by t.id ASC
			");
			
			$query->setParameter('language_id', $language_id);
			
			/*build the exact translations mapping*/		
			$exact = array();
			
			foreach($query->getResult() as $translation)
			{
				$exact[$translation->getMkey()] = $translation;
			}
			
			/*build the displaced translations mapping*/
			$displaced = array();
			$approx    = array();
			foreach($exact as $t)
			{
				if($m = $this->em->getRepository('FMSymSlateBundle:Message')->findOneByMkey($t->getMkey()))
				{
					$displaced[$m->getText()] = $t;
					$approx[$this->normalize($m->getText())] = $t;
				}
			}
			
			/*GET the messages that have no translation*/
			$query = $this->em->createQuery("
				SELECT m FROM FMSymSlateBundle:Message m LEFT JOIN m.current_translations ct
				WITH ct.language_id = :language_id
				WHERE ct.id IS NULL
			");
			$query->setParameter('language_id', $language_id);
			
			$messages = $query->getResult();
			
			foreach($messages as $message)
			{
				$translation = null;
				
				if(isset($exact[$message->getMkey()]) and !$exact[$message->getMkey()]->getHasError())
				{
					$translation = $exact[$message->getMkey()];
				}
				else if(isset($displaced[$message->getText()]) and !$displaced[$message->getText()]->getHasError())
				{
					$translation = $displaced[$message->getText()];
				}
				else if(isset($approx[$this->normalize($message->getText())]) and !$approx[$message->getText()]->getHasError())
				{
					$translation = $approx[$this->normalize($message->getText())];
				}
				
				if($translation)
				{
					$ct = new \FM\SymSlateBundle\Entity\CurrentTranslation();
					$ct->setTranslation($translation);
					$ct->setLanguage($translation->getLanguage());
					$ct->setMessage($message);
					$this->em->persist($ct);
				}

				$this->step();

			}

			$this->em->flush();
			$this->em->clear();

			$this->setStatus("Completed!");
			
		}
	}

}
