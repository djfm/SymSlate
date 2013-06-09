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

		foreach($storages as $storage)
		{

			$cts = $storage->getMessage()->getCurrentTranslations();
			
			if($cts->count() == 1)
			{
				$ct = $cts[0];
				$translation = $ct->getTranslation();

				$validation = $this->validator->validate($storage->getMessage()->getText(), $translation->getText(), $language, $storage->getCategory());

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
						$file_contents[$path] = $translation->getText();
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
			else
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
		
		$archive = new \Archive_Tar($export->getAbsolutePath(), 'gz');
		
		foreach($file_contents as $path => $data)
		{
			$archive->addString($path, $data);
		}

		$this->setStatus("Completed!");

		return $export->getAbsolutePath();
		
	}

}
