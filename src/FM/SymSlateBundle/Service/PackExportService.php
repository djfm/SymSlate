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
		
		$language = $this->em->getRepository('FMSymSlateBundle:Language')->find($export->getLanguageId());
		$storages = $this->em->getRepository('FMSymSlateBundle:Pack')->getStoragesWithTranslations($export->getPack()->getId(),$export->getLanguageId());

		$this->setExpectedSteps(count($storages));

		$pack = $export->getPack();

		$this->em->clear();

		if($pack->getPackType() == 'standard')
		{
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
								
							$file_contents[$path] .= "{$array}['" . addslashes($storage->getMessage()->getMkey()) . "'] = '" . addslashes($translation->getText()) . "';\n";
							
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
			
			if(count($file_contents) > 0)
			{
				$export   = $this->em->getRepository('FMSymSlateBundle:PackExport')->find($pack_export_id);
				$export->setFilepath($export->getId() . "_" . $language->getAName() . "_" . $export->getPack()->getFullName() . ".gzip");
				$archive = new \Archive_Tar($export->getAbsolutePath(), 'gz');
				
				foreach($file_contents as $path => $data)
				{
					$archive->addString($path, $data);
				}
			}
		}
		else if($pack->getPackType() == 'installer')
		{

			$general_messages = array();
			$xml = array();

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
						if($storage->getCategory() == "General Messages")
						{
							$general_messages[$storage->getMessage()->getMkey()] = $translation->getText();
						}
						else if($storage->getCategory() == "XML")
						{
							$path = str_replace('[iso]', $language->getCode(), $storage->getPath());
							$m    = array();
							preg_match('/\/([^\/\.]+?)\.xml$/', $path, $m);
							$entity = $m[1];
							
							if(!isset($xml[$path]))$xml[$path] = array('entity_name' => $entity, 'entities' => array());

							if($storage->getMethod() == 'FILE_RAW_XML')
							{
								$xml[$path]["entities"][] = $translation->getText();
							}
							else if($storage->getMethod() == 'FILE_XML')
							{
								$xml[$path]["entities"][] = str_replace('[translation]', $translation->getText(), $storage->getCustom());
							}
							/*
							if($entity == "profile"){
								print_r(end($xml[$path]["entities"]));
								echo "\n";
								print_r($storage->getCustom());
							}*/
						}
					}
				}
				else
				{
					throw new Exception("Classification ". $cl->getId() . " doesn't have exactly one current translation in this language! (got " . $cts->count() . ")");	
				}

				$this->step();
			}

		}

		//die();

		$files = array();

		$first = true;
		$general_messages_file_text = "<?php\nreturn array(\n'translations' => array(\n";
		foreach($general_messages as $key => $value)
		{
			if($first)$first=false;
			else $general_messages_file_text .= ",\n";
			$general_messages_file_text .= "'" . addslashes($key) . "'" . " => " . "'" . addslashes($value) . "'";
		}
		$general_messages_file_text .= '));';

		$files['langs/' . $language->getCode() . '/install.php'] = $general_messages_file_text;
		
		foreach($xml as $path => $arr)
		{
			$entity_name = $arr['entity_name'];

			$files[$path] = '<?xml version="1.0"?>' . "\n<entity_".$entity_name.">";
			foreach($arr['entities'] as $entity)
			{
				$files[$path] .= "\n\n$entity";
			}
			$files[$path] .= "\n</entity_".$entity_name.">";
			
		}

		$export   = $this->em->getRepository('FMSymSlateBundle:PackExport')->find($pack_export_id);
		$export->setFilepath($export->getId() . "_" . $language->getAName() . "_" . $export->getPack()->getFullName() . ".gzip");
		$archive = new \Archive_Tar($export->getAbsolutePath(), 'gz');
		
		foreach($files as $path => $data)
		{
			$archive->addString($path, $data);
		}

		$this->setStatus("Completed!");

		return $export->getAbsolutePath();
		
	}

}
