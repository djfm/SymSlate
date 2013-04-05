<?php

namespace FM\SymSlateBundle\Service;

class TranslationsImportService extends \FM\SymSlateBundle\Worker\Worker
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
		//$start_mem = round(memory_get_usage() / 1024/1024);
		//$this->logger->info("Memory usage before import: " . $start_mem . "MB");

		$this->setStatus("Initializing...");
		$translations_import_id = $args['translations_import_id'];

		$this->em->getConnection()->getConfiguration()->setSQLLogger(null);
		
		$translations_import = $this->em->getRepository('FMSymSlateBundle:TranslationsImport')->findOneById($translations_import_id);
		$user                = $translations_import->getCreator();
		$translations        = $translations_import->buildTranslations($this->logger);

		//$this->logger->info("Memory usage after translations generation: " . round(memory_get_usage() / 1024/1024) . "MB (started at: $start_mem)");
		$num_translations = count($translations);
		//$this->logger->info("There are $num_translations to import!");
		$this->setExpectedSteps($num_translations);
		$this->setStatus("Running...");

		foreach($translations as $key => $translation)
		{	
			if(($language = $this->getOrCreateLanguage($user, $translation->language_code)) and $user->canTranslateInto($language))
			{
				$validation = $this->validator->validate(null, $translation->getText(), $language, null);

				if(!$validation['success'])continue;

				$brand_new_translation = true;
				if($tmp = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneBy(array(
					"mkey" => $translation->getMkey(),
					"language_id" => $language->getId(),
					"text" => $translation->getText()
				)))
				{
					//don't update already existing translations

					$translation = $tmp;
					$brand_new_translation = false;
				}

				if($brand_new_translation or $args['force_actualize'])
				{
					if($brand_new_translation)
					{
						$translation->setTranslationsImport($translations_import);
						$translation->setAuthor($user);
						$translation->setLanguage($language);
					}
					
					$this->em->persist($translation);
					$this->em->getRepository('FMSymSlateBundle:CurrentTranslation')->actualizeWith($translation, $this->logger);
					$this->em->flush();
				}
			}

			
			
			$this->step();

			unset($translations[$key]);

			if($this->em->getUnitOfWork()->size() > 250)
			{
				//$this->logger->info("[UOW size before clear: " . $this->em->getUnitOfWork()->size() . "]");
				//$this->logger->info("Memory usage before clear: " . round(memory_get_usage() / 1024/1024) . "MB");
				//free some memory, without this we explode!!
				$this->em->clear();

				//reload entities because of the "clear" just before
				$translations_import = $this->em->getRepository('FMSymSlateBundle:TranslationsImport')->findOneById($translations_import_id);
				$user                = $translations_import->getCreator();

				//$this->logger->info("[UOW size after clear: " . $this->em->getUnitOfWork()->size() . "]");
				//$this->logger->info("Memory usage after clear: " . round(memory_get_usage() / 1024/1024) . "MB\n");
			}

		}
		$this->setStatus("Done!");
	}

}
