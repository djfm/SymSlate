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
		$translations        = $translations_import->buildTranslations($args['version'], $this->logger);

		//$this->logger->info("Memory usage after translations generation: " . round(memory_get_usage() / 1024/1024) . "MB (started at: $start_mem)");
		$num_translations = count($translations);
		//$this->logger->info("There are $num_translations to import!");
		$this->setExpectedSteps($num_translations);
		$this->setStatus("Running...");

		foreach($translations as $key => $translation)
		{	
			if($language = $this->getOrCreateLanguage($user, $translation['language_code']))
			{
				$submission = array(
					'user' 						=> $user,
					'language' 					=> $language,
					'mkey'              		=> $translation['mkey'],
					'translation_text'  		=> $translation['translation_text'],
					'overwrite_current' 		=> $args['force_actualize'],
					'translations_import' 		=> $translations_import
				);

				$val = $this->submitter->submit($submission);
				if($val['success'] === false)
				{
					die(print_r($val, 1));
				}
			}
			
			$this->step();

			unset($translations[$key]);

			if($this->em->getUnitOfWork()->size() > 250)
			{
				//free some memory, without this we explode!!
				$this->em->clear();

				//reload necessary entities because of the "clear" just before
				$translations_import = $this->em->getRepository('FMSymSlateBundle:TranslationsImport')->findOneById($translations_import_id);
				$user                = $translations_import->getCreator();
			}

		}
		$this->setStatus("Done!");
	}

}
