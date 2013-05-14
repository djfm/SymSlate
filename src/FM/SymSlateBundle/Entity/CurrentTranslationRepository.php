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
		//see if there is a message corresponding to this translation
		if($message = $this->getEntityManager()->getRepository('FMSymSlateBundle:Message')->findOneByMkey($translation->getMkey()))
		{
			//see if it is already associated to a translation
			$ct = $this->findOneBy(array('message_id' => $message->getId(), 'language_id' => $translation->getLanguage()->getId()));
			if(null === $ct)
			{
				$ct = new CurrentTranslation();
				$ct->setMessage($message);
				$ct->setLanguage($translation->getLanguage());
			}

			$ct->setTranslation($translation);
			$this->getEntityManager()->persist($ct);

			if($logger)$logger->info("Changed translation to: \n".$translation->getText());
		}
	}
}