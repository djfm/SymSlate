<?php

namespace FM\SymSlateBundle\Job;

class TranslationsImportJob extends \FM\Bundle\SlowShowBundle\Job\Job
{
	public function perform($arguments)
	{
		$this->em->getRepository('FMSymSlateBundle:TranslationsImport')->saveTranslations($arguments['translations_import_id']);
	}
}
