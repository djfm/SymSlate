<?php

namespace FM\SymSlateBundle\Service;

class PackExportService extends \FM\Bundle\SlowShowBundle\Worker\Worker
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

		$file_contents = array();
		$footers       = array();
		
		foreach($storages as $storage)
		{
			$cts = $storage->getMessage()->getCurrentTranslations();
			if($cts->count() == 1)
			{
				$ct = $cts[0];
				$translation = $ct->getTranslation();
				
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
			$export->setFilepath($export->getId() . "_" . $language->getAName() . "_" . $export->getPack()->getFullName() . ".gzip");
			$archive = new \Archive_Tar($export->getAbsolutePath(), 'gz');
			
			foreach($file_contents as $path => $data)
			{
				$archive->addString($path, $data);
			}
		}

		$this->setStatus("Completed!");

		return $export->getAbsolutePath();
		
	}

}