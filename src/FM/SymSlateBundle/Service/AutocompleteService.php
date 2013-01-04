<?php

namespace FM\SymSlateBundle\Service;

class AutocompleteService
{
	public function __construct($em, $security_context, $logger)
	{
		$this->em = $em;
		$this->security_context = $security_context;
		$this->logger = $logger;
	}

	public function normalize($str)
	{
		$nz = strtolower($str);
		$nz = preg_replace('/[:\.;!-]+/',' ',$str);
		$nz = preg_replace('/\s+/',' ',$str);
		return $nz;
	}

	public function run($args)
	{
		$language_ids = isset($args['language_ids']) ? $args['language_ids'] : null;

		if( null === $language_ids or count($language_ids) == 0)$language_ids = array_map(function($language){
			return $language->getId();
		},
		$this->em->getRepository('FMSymSlateBundle:Language')->findAll());
		
		foreach($language_ids as $language_id)
		{
						
			$query = $this->em->createQuery("
				SELECT t, MAX(t.id) as maxid FROM FMSymSlateBundle:Translation t WHERE t.language_id = :language_id
				GROUP BY t.mkey
				HAVING t.id = maxid
				ORDER by maxid ASC
			");
			
			$query->setParameter('language_id', $language_id);
			
			/*build the exact translations mapping*/		
			$exact = array();
			
			foreach($query->getResult() as $translation)
			{
				$exact[$translation[0]->getMkey()] = $translation[0];
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
				
				if(isset($exact[$message->getMkey()]))
				{
					$translation = $exact[$message->getMkey()];
				}
				else if(isset($displaced[$message->getText()]))
				{
					$translation = $displaced[$message->getText()];
				}
				else if(isset($approx[$this->normalize($message->getText())]))
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
			}

			$this->em->flush();
			
		}
	}

}