<?php

namespace FM\SymSlateBundle\Service;

class TranslationsImportService extends \FM\Bundle\SlowShowBundle\Worker\Worker
{

	public function getOrCreateLanguage($user, $language_code)
	{	
		if($language = $this->em->getRepository('FMSymSlateBundle:Language')->findOneByCode($language_code))
		{
			return $language;
		}
		else if($user->canCreateLanguages())
		{
			$language = new \FM\SymSlateBundle\Entity\Language();
			$language->setCode($language_code);
			$this->em->persist($language);
			$this->em->flush();
			return $language;
		}
		else return null;
	}

	public function run($args)
	{
		$this->setStatus("Initializing...");
		$translations_import_id = $args['translations_import_id'];

		$this->em->getConnection()->getConfiguration()->setSQLLogger(null);
		
		$translations_import = $this->em->getRepository('FMSymSlateBundle:TranslationsImport')->findOneById($translations_import_id);
		$user                = $translations_import->getCreator();
		$translations        = $translations_import->buildTranslations($this->logger);
		
		$this->setExpectedSteps(count($translations));

		foreach($translations as $translation)
		{	
			if(($language = $this->getOrCreateLanguage($user, $translation->language_code)) and $user->canTranslateInto($language))
			{
				if($tmp = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneBy(array(
					"mkey" => $translation->getMkey(),
					"language_id" => $language->getId(),
					"text" => $translation->getText()
				)))
				{
					$translation = $tmp;
				}
				else
				{
					$translation->setTranslationsImport($translations_import);
					$translation->setAuthor($user);
					$translation->setLanguage($language);
					$this->em->persist($translation);
					//only actualize for new translations
					$this->em->getRepository('FMSymSlateBundle:CurrentTranslation')->actualizeWith($translation, $this->logger);
				}
				
				$this->em->flush();
				$this->em->clear();
				$translations_import = $this->em->getRepository('FMSymSlateBundle:TranslationsImport')->findOneById($translations_import_id);
				$user                = $translations_import->getCreator();
			}

			$this->step();

		}
	}

}