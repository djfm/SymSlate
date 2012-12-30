<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CurrentTranslationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CurrentTranslationRepository extends EntityRepository
{
	public function actualizeWith($translation, $logger=null)
	{
		if($logger)$logger->info("Actualizing translation with translation with key: ".$translation->getMkey());


		//see if there is a message corresponding to this translation
		if($message = $this->getEntityManager()->getRepository('FMSymSlateBundle:Message')->findOneByMkey($translation->getMkey()))
		{
			//see if it is already associated to a translation
			if($ct = $this->findOneBy(array('message_id' => $message->getId(), 'language_id' => $translation->getLanguageId())))
			{
				//don't update
			}
			else
			{
				$ct = new CurrentTranslation();
				$ct->setMessage($message);
				$ct->setTranslation($translation);
				$ct->setLanguage($translation->getLanguage());
				$this->getEntityManager()->persist($ct);
			}
		}

		/*
		$query = $this->getEntityManager()->createQuery('SELECT c from FMSymSlateBundle:Classification c JOIN c.message m WHERE m.mkey = :mkey');
		$query->setParameter('mkey',$translation->getMkey());
		$classifications = $query->getResult();
		if($logger)$logger->info("CurrentTranslations to create or update: ".count($classifications));
		
		$cts = array();
		
		foreach($classifications as $classification)
		{
			if($ct = $this->findOneBy(array("classification_id" => $classification->getId(), "language_id" => $translation->getLanguage()->getId())))
			{
				//update CurrentTranslation
				//deferred till after the if because this is done in both cases
			}
			else
			{
				$ct = new CurrentTranslation();
				$ct->setClassification($classification);
				$ct->setLanguageId($translation->getLanguage()->getId());
			}
			
			$ct->setTranslation($translation);
			$this->getEntityManager()->persist($ct);
			
			$cts[] = $ct;
		}
		
		return $cts;*/
	}
}