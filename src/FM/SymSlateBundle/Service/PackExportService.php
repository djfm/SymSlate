<?php

namespace FM\SymSlateBundle\Service;

class PackExportService extends \FM\SymSlateBundle\Worker\Worker
{

	public function run($args)
	{
		$this->setStatus("Started...");

		$this->logger->info("RUNNING EXPORT PACK");
		$pack_export_id = $args['pack_export_id'];

		$export   = $this->em->getRepository('FMSymSlateBundle:PackExport')->find($pack_export_id);
		
		$language = $export->getLanguage();
		$storages = $this->em->getRepository('FMSymSlateBundle:Pack')->getStoragesWithTranslations($export->getPack()->getId(),$language->getId());

		$this->setExpectedSteps(count($storages));

		$pack = $export->getPack();

		$this->em->clear();		
		
		$file_contents = array();
		$footers       = array();
		$po_files      = array();

		foreach($storages as $storage)
		{

			$cts = $storage->getMessage()->getCurrentTranslations();
			
			if($cts->count() == 1)
			{
				$ct = $cts[0];
				$translation = $ct->getTranslation();

				if(!$translation)
				{
					// This happends when the Storage has a CurrentTranslation
					// but the CurrentTranslation doesn't have a translation
					// because it is invalid.
					// The CurrentTranslation is still there because of the Left Join in getStoragesWithTranslations
					// but since the associations are fetched greedily $ct->getTranslation() returns null!!
					continue;
				}

				$message_text = $storage->getMessage()->getText();
				$translation_text = $translation->getText();

				$validation = $this->validator->validate($message_text, $translation_text, $language, $storage->getCategory());

				if($validation['success'])
				{
				
					$path = str_replace('[iso]', $language->getCode(), $storage->getPath());
					if($path[0] == '/')$path = substr($path, 1);
					
					if($storage->getMethod() == 'ARRAY')
					{
						$array = $storage->getCustom();
						if(!isset($file_contents[$path]))
						{
							if($storage->getCategory() != 'Tabs')
							{
								$file_contents[$path] = <<<NOW
<?php

global $array;
$array = array();


NOW;
							}
							else
							{
								$file_contents[$path] = <<<NOW
<?php

$array = array();


NOW;

								$footers[$path] = "\nreturn $array;";

							}
						}
								
						$file_contents[$path] .= "{$array}['" . addslashes(trim($storage->getMessage()->getMkey())) . "'] = '" . addslashes(trim($translation->getText())) . "';\n";
						
					}
					else if($storage->getMethod() == 'FILE')
					{
						if($storage->getCategory() === 'Mails2')
						{
							foreach( explode(",", $storage->getPath()) as $path) 
							{
								$path = str_replace('[iso]', $language->getCode(), $path);
								if($path[0] == '/')$path = substr($path, 1);
								if(!isset($po_files[$path]))
								{
									$po_files[$path] = array();
								}
								$po_files[$path][$storage->getMessage()->getText()] = $translation->getText();
							}
						}
						else
						{
							$file_contents[$path] = $translation->getText();
						}
					}
					else
					{
						throw new Exception("Unknown storage method: " . $storage->getMethod());	
					}
				}
				else
				{
					//the Entity Manager was cleared so we need to fetch the translation again before updating it
					$translation = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneById($translation->getId());
					$translation->setHasError(true);
					$translation->setErrorMessage($validation['error_message']);
					$this->em->persist($translation);
					$this->em->flush();
					$this->em->clear();
				}

					
				if(isset($validation['warning_message']) or $translation->getHasWarning())
				{
					$translation = $this->em->getRepository('FMSymSlateBundle:Translation')->findOneById($translation->getId());
					if(isset($validation['warning_message']))
					{
						$translation->setHasWarning(true);
						$translation->setWarningMessage($validation['warning_message']);
					}
					else
					{
						$translation->setHasWarning(false);
						$translation->setWarningMessage('');
					}
					$this->em->persist($translation);
					$this->em->flush();
					$this->em->clear();
				}
				
			}
			else if($cts->count() === 0)
			{
				if($storage->getMethod() === 'FILE' && $storage->getCategory() === 'Mails2')
				{
					//add empty strings to the .po to
					foreach( explode(",", $storage->getPath()) as $path) 
					{
						$path = str_replace('[iso]', $language->getCode(), $path);
						if($path[0] == '/')$path = substr($path, 1);
						if(!isset($po_files[$path]))
						{
							$po_files[$path] = array();
						}
						$po_files[$path][$storage->getMessage()->getText()] = '';
					}
				}
			}
			else // Zero, One, Insanity
			{
				throw new Exception("Classification ". $cl->getId() . " doesn't have exactly one current translation in this language! (got " . $cts->count() . ")");	
			}

			$this->step();
				
		}

		foreach($footers as $path => $data)
		{
			$file_contents[$path] .= $data;
		}

		$export   = $this->em->getRepository('FMSymSlateBundle:PackExport')->find($pack_export_id);

		//Create The Directory Tree (if needed)
		$dirs     = array($export->getPack()->getProject(), $export->getPack()->getVersion());
		$path     = $export->getUploadRootDir();
		foreach($dirs as $dir)
		{
			$path .= '/'.$dir;
			if(!is_dir($path))
			{
				mkdir($path);
			}
		}

		$export->setFilepath(implode('/', $dirs) . '/' . $language->getCode() . ".gzip");
		
		if(file_exists($export->getAbsolutePath()))
		{
			//Archive_Tar will add to the already existing archive if we do not delete it
			unlink($export->getAbsolutePath());
		}

		$archive = new \Archive_Tar($export->getAbsolutePath(), 'gz');
		
		foreach($file_contents as $path => $data)
		{
			$archive->addString($path, $data);
		}

		foreach ($po_files as $path => $dictionary)
		{
			$po = new \FM\SymSlateBundle\PO\PoFile();
			$po->addMessages($dictionary);
			$archive->addString($path, (string)$po);
		}

		$this->setStatus("Completed!");

		$this->em->persist($export);
		$this->em->flush();

		return $export->getAbsolutePath();
		
	}

}
